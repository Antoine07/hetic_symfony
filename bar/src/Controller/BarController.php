<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Contracts\HttpClient\HttpClientInterface;

use App\Entity\{Beer, Category, Country, Statistic, Client};
use App\Services\Hello;
use App\Services\HelperParser;
use App\Services\QuoteService;
use cebe\markdown\Markdown;

class BarController extends AbstractController
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/mentions", name="mentions")
     */
    public function mentions()
    {

        return $this->render('mentions/index.html.twig', [
            'title' => 'Mentions légales',
        ]);
    }

    /**
     * @Route("/category/{id}", name="category")
     */
    public function showCategory(int $id)
    {
        $catRepo = $this->getDoctrine()->getRepository(Category::class);
        $category = $catRepo->find($id);

        return $this->render('category/index.html.twig', [
            'title' => $category->getName(),
            'beers' => $category->getBeers(),
        ]);
    }

    /**
     * @Route("/country/{id}", name="country")
     */
    public function showCountry(int $id)
    {
        $countryRepo = $this->getDoctrine()->getRepository(Country::class);
        $countryegory = $countryRepo->find($id);

        return $this->render('country/index.html.twig', [
            'title' => $countryegory->getName(),
            'beers' => $countryegory->getBeers(),
        ]);
    }

    /**
     * @Route("/statistic", name="statistic")
     */
    public function showStat()
    {
        $statRepo = $this->getDoctrine()->getRepository(Statistic::class);

        extract($statRepo->statInfo()[0]);

        return $this->render('statistic/index.html.twig', [
            'title' => 'Stat',
            'clients' => $statRepo->conso(),
            'total_beer' => $nb_beer,
            'total_client' => $nb_client,
            'avg' => $avg,
            'min' => $min,
            'max' => $max,
            'std' => $std
        ]);
    }

    /**
     * @Route("/beer/{id}", name="beer")
     */
    public function showBeer(int $id)
    {
        $beerRepo = $this->getDoctrine()->getRepository(Beer::class);
        $beeregory = $beerRepo->find($id);
        $catRepo = $this->getDoctrine()->getRepository(Category::class);

        return $this->render('beer/index.html.twig', [
            'title' => $beeregory->getName(),
            'beer' => $beeregory,
            'specials' => $catRepo->findCatSpecial($id)
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {

        $beerRepo = $this->getDoctrine()->getRepository(Beer::class);

        return $this->render('home/index.html.twig', [
            'title' => "Page d'accueil",
            'beers' => $beerRepo->findLastBeer(3),
        ]);
    }

    /**
     * @Route("/menu", name="menu")
     */
    public function mainMenu(string $routeName, string $category_id): Response
    {
        $catRepo = $this->getDoctrine()->getRepository(Category::class);

        return $this->render('partials/main_menu.html.twig', [
            'title' => "Page d'accueil",
            'categories' => $catRepo->findByTerm('normal'),
            'route_name' => $routeName,
            'category_id' => $category_id
        ]);
    }

    /**
     * @Route("/showService", name="showService")
     */
    public function showService(Hello $hello, HelperParser $translate)
    {

        $markdowns = [
            '1' => <<<EOT
# Recette nouvelle bière
* Pommes
* Poires
    * Sous élément avec au moins quatre espaces devant.
EOT,
            '2' => <<<EOT
# Deuxième recette de bière
* Poires
* Pommes
    * Sous élément avec au moins quatre espaces devant.
* Houblon
EOT,
];

        return $this->render('showService/index.html.twig', [
            'title' => 'Show service',
            'message' => $hello->say(),
            'recipes' => $translate->translateHtml($markdowns)
        ]);
    }

     /**
     * @Route("/quotes", name="quotes")
     */
    public function quotes(QuoteService $quote){

        // dd($quote->getQuotes());

        return $this->render('quotes/index.html.twig', [
            'title' => 'Show service',
            'quotes' => $quote->getQuotes()
           
        ]);
    }

}
