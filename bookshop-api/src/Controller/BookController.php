<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Book;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    // public function index(): Response
    // {
    //     return $this->render('book/index.html.twig', [
    //         'controller_name' => 'BookController',
    //     ]);
    // }
    public function createBook(ManagerRegistry $doctrine,ValidatorInterface $validator): Response
    {
        $entityManager = $doctrine->getManager();

        $book = new Book();
        $book->setTitle('Title1');
        $book->setPrice(20);
        $book->setAuthor('Vincent');
        $book->setDescription('Ergonomic and stylish!');

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($book);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        $errors = $validator->validate($book);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        return new Response('Saved new product with id '.$book->getId());
    }
    
    #[Route('/book/{id}', name: 'app_book')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $book = $doctrine->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $minPrice = 1000;

        $book = $doctrine->getRepository(Book::class)->findAllGreaterThanPrice($minPrice);

        return new Response('Check out this great product: '.$book->getTitle());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }

//     #[Route('/update/{id}', name: 'app_book')]

//    public function update(ManagerRegistry $doctrine, int $id): Response
//     {
//         $entityManager = $doctrine->getManager();
//         $book = $entityManager->getRepository(Book::class)->find($id);

//         if (!$book) {
//             throw $this->createNotFoundException(
//                 'No product found for id '.$id
//             );
//         }

//         $book->setTitle('New !');
//         $entityManager->flush();

//         return $this->redirectToRoute('app_book', [
//             'id' => $book->getId()
//         ]);
//     }

    
}