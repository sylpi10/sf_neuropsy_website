<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

/**
 * Convertit les images uploadées (JPEG / PNG) en WebP et limite leur largeur.
 * En cas d'échec (GD sans WebP, fichier illisible...), l'image d'origine est conservée.
 */
class ImageOptimizer
{
    private const QUALITY = 80;

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @return string le nom du fichier final (inchangé si rien n'a été fait)
     */
    public function optimize(string $directory, string $fileName, int $maxWidth): string
    {
        $sourcePath = $directory . '/' . $fileName;
        $info = @getimagesize($sourcePath);

        if (!$info || !function_exists('imagewebp')) {
            return $fileName;
        }

        [$width, , $type] = $info;

        // Déjà optimisée : on ne ré-encode pas un WebP (perte de qualité à chaque passage)
        if ($type === IMAGETYPE_WEBP && $width <= $maxWidth) {
            return $fileName;
        }

        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($sourcePath),
            default => false,
        };

        if (!$image) {
            return $fileName;
        }

        if ($type === IMAGETYPE_JPEG) {
            $image = $this->applyExifOrientation($image, $sourcePath);
        }

        if (!imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        // imagecopyresampled plutôt qu'imagescale : IMG_BICUBIC renvoie false selon les versions de GD
        if (imagesx($image) > $maxWidth) {
            $height = (int) round(imagesy($image) * $maxWidth / imagesx($image));
            $resized = imagecreatetruecolor($maxWidth, $height);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $maxWidth, $height, imagesx($image), imagesy($image));
            imagedestroy($image);
            $image = $resized;
        }

        // Conserve la transparence des PNG
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $targetName = pathinfo($fileName, PATHINFO_FILENAME) . '.webp';
        $targetPath = $directory . '/' . $targetName;
        $tmpPath = $targetPath . '.tmp';

        $written = imagewebp($image, $tmpPath, self::QUALITY);
        imagedestroy($image);

        if (!$written || !rename($tmpPath, $targetPath)) {
            @unlink($tmpPath);
            $this->logger->warning('Conversion WebP impossible pour {file}', ['file' => $sourcePath]);

            return $fileName;
        }

        @chmod($targetPath, 0644);

        if ($targetPath !== $sourcePath) {
            @unlink($sourcePath);
        }

        return $targetName;
    }

    /**
     * Les photos de téléphone sont souvent stockées "couchées" avec une balise EXIF d'orientation :
     * GD l'ignore, il faut donc redresser l'image avant de la ré-encoder.
     */
    private function applyExifOrientation(\GdImage $image, string $path): \GdImage
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = @exif_read_data($path)['Orientation'] ?? 1;

        $angle = match ((int) $orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        if (!$rotated) {
            return $image;
        }

        imagedestroy($image);

        return $rotated;
    }
}
