<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genus;
use http\Message\Body;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GenusController extends Controller
{

    /**
     * @Route("/genus/new")
     */
    public function newAction()
    {
        $genus = new Genus();
        $genus->setName('Octopus'.rand(1, 100)); // Using the setter method in the Genus entity class to give the property some values
        $genus->setSubFamily('Octopodinae');
        $genus->setSpeciesCount(rand(100, 9999));

        $em = $this->getDoctrine()->getManager(); // Getting the doctrine entity manager and assigning it to $em to easily use it.
        $em->persist($genus); // Saving the newly created $genus object to the database using the entity manager
        $em->flush();

        return new Response('<html><body>Genus Created</body></html>');
    }

    /**
     * @Route("/genus")
     */
    public function listAction() // Making a query to get a list of genuses from the database.
    {
        $em = $this->getDoctrine()->getManager();
        $genuses = $em->getRepository(Genus::class) // Creating a repository object with the argument that is the class I want to query from. In this case, the genus class.
            ->findAllPublishedOrderedBySize();

        return $this->render('genus/list.html.twig', [ // Rendering and returning the list view, which I am passing a variable so that we can target the genuses in the list view
            'genuses' => $genuses,
        ]);


    }

    // Giving this route below a name so that it can be easily targeted inside of twig
    /**
     * @Route("/genus/{genusName}", name="genus_show")
     */
    public function showAction($genusName)
    {
        $em = $this->getDoctrine()->getManager();
        $genus = $em->getRepository(Genus::class)
            ->findOneBy(['name' => $genusName]); // Takes an array of things to find by.
                                                // 'name' is the variable or database column and we want the name it finds to be put in the object $genusName

        if (!$genus) { // if $genus is not an object, meaning that the name could not be found in the database.
            throw $this->createNotFoundException('No genus found'); // Throw a 404 not found exception with this message. This will not show in production
        }

//        $cache = $this->get('doctrine_cache.providers.my_markdown_cache');
//        $key = md5($funFact);
//        if ($cache->contains($key)) {
//            $funFact = $cache->fetch($key);
//        } else {
//            sleep(1); // fake how slow this could be
//            $funFact = $this->get('markdown.parser')
//                ->transform($funFact);
//            $cache->save($key, $funFact);
//        }
//
//        $this->get('logger')
//            ->info('Showing genus: '.$genusName);

        return $this->render('genus/show.html.twig', array(
            'genus' => $genus, // Since we have a $genus object that is the database repo, we can just call it in the return because it will hold all the information we need.
        ));
    }

    /**
     * @Route("/genus/{genusName}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction($genusName)
    {
        $notes = [
            ['id' => 1, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me', 'date' => 'Dec. 10, 2015'],
            ['id' => 2, 'username' => 'AquaWeaver', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2015'],
            ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
        ];
        $data = [
            'notes' => $notes
        ];

        return new JsonResponse($data);
    }
}
