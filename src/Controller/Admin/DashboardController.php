<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\EditableText;
use App\Entity\Fee;
use App\Entity\Formation;
use App\Entity\Information;
use App\Entity\MenuItem as MenuItemEntity;
use App\Entity\Page;
use App\Entity\SitePageContent;
use App\Entity\OpeningDay;
use App\Entity\SchedulePeriod;
use App\Entity\Slide;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: "/backoffice", routeName: "admin")]
#[IsGranted("ROLE_ADMIN")]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect(
            $adminUrlGenerator
                ->setController(FormationCrudController::class)
                ->generateUrl(),
        );

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute("admin_user_index");
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle("Emmanuelle Mir");
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard("Dashboard", "fa fa-home");
        yield MenuItem::section("Site");
        yield MenuItem::linkToCrud("Menu", "fa fa-bars", MenuItemEntity::class);
        yield MenuItem::linkToCrud("Textes des pages", "fa fa-pen-to-square", EditableText::class);
        yield MenuItem::linkToCrud("Pages créées", "fa fa-file-lines", Page::class);
        yield MenuItem::linkToCrud("Sections ajoutées aux pages", "fa fa-puzzle-piece", SitePageContent::class);
        yield MenuItem::linkToCrud("Slider page d'accueil", "fa fa-images", Slide::class);

        yield MenuItem::section("Infos pratiques");
        yield MenuItem::linkToCrud("Honoraires", "fa fa-euro-sign", Fee::class);
        yield MenuItem::linkToCrud("Jours de consultation", "fa fa-calendar-week", OpeningDay::class);
        yield MenuItem::linkToCrud("Fermetures / périodes", "fa fa-calendar-xmark", SchedulePeriod::class);
        yield MenuItem::linkToCrud("Coordonnées et options", "fa fa-info-circle", Information::class);

        yield MenuItem::section("Formations");
        yield MenuItem::linkToCrud("Formations", "fa fa-graduation-cap", Formation::class);
        yield MenuItem::linkToCrud("Catégories de formations", "fa fa-folder-open", Category::class);

        yield MenuItem::section("Compte");
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $changePasswordUrl = $adminUrlGenerator
            ->setRoute("admin_account_password")
            ->generateUrl();

        yield MenuItem::linkToUrl(
            "Changer mot de passe",
            "fa fa-key",
            $changePasswordUrl,
        );
        yield MenuItem::linkToLogout("Logout", "fa fa-sign-out");
    }
}
