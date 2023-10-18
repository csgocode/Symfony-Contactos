<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contactos;
use App\Entity\Provincia;
use LDAP\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactoController extends AbstractController
{
    private $contactos = [
        1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],
        2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],
        5 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],
        7 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],
        9 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"]
    ];

    #[Route('/contacto/insertarProv', name: 'insertarProv')]
    public function insertarProv(ManagerRegistry $doctrine): Response {
        $entityManager = $doctrine->getManager();
        $provincia = new Provincia();
        $provincia->setNombre("Castellon");
        $contacto = new Contactos();
        $contacto->setNombre("Pepe");
        $contacto->setTelefono("666666666");
        $contacto->setEmail("pepe@pepe.com");
        $contacto->setProvincia($provincia);
        $entityManager->persist($provincia);
        $entityManager->persist($contacto);
        $entityManager->flush();
        return $this->render('contacto/contacto.html.twig', [
            'contacto' => $contacto
        ]);

    }
    
    #[Route('/contacto/{codigo}', name: 'ficha_contacto')]
    public function ficha(ManagerRegistry $doctrine, $codigo): Response {
        $repositorio = $doctrine->getRepository(Contactos::class);
        $contacto = $repositorio->find($codigo);

        return $this->render('contacto/index.html.twig', [
            'contacto' => $contacto
        ]);
    }

    #[Route('/contacto/buscar/{texto}', name: 'buscarText')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response {
        $repositorio = $doctrine->getRepository(Contactos::class);
        $contactos = $repositorio->findByName($texto);

        return $this->render('contacto/index2.html.twig', [
            'contactos' => $contactos
        ]);
    }

    #[Route('/contacto/update/{id}/{nombre}', name: 'modificarContacto')]
    public function updateContact(ManagerRegistry $doctrine, $id, $nombre): Response {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contactos::class);
        $contacto = $repositorio->find($id);

        if ($contacto) {
            $contacto->setNombre($nombre);
            try {
                $entityManager->flush();
                return $this->render('contacto/index.html.twig', [
                    'contacto' => $contacto
                ]);
            } catch (\Exception $e) {
                return new Response("Error insertando objetos.");
            }
        } else{

        return $this->render('contacto/index2.html.twig', [
            'contacto' => null
        ]);
    } 
}


    #[Route('/contacto/delete/{id}', name: 'modificarContacto')]
    public function deleteContact(ManagerRegistry $doctrine, $id): Response {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contactos::class);
        $contacto = $repositorio->find($id);

        if ($contacto) {
            try {
                $entityManager->remove($contacto);
                $entityManager->flush();

                return new Response("Contacto borrado con exito-");
            } catch (\Exception $e) {
                return new Response("Error borrando tu contacto.");
            }
        } else{

        return $this->render('contacto/index2.html.twig', [
            'contacto' => null
        ]);
    } 
    }
       
       

    #[Route('/contactos/insertar', name: 'insertar')]
    public function insertar(ManagerRegistry $doctrine) {
        $entityManager = $doctrine->getManager();
        foreach ($this->contactos as $c) {
            $contacto = new Contactos();
            
            $contacto->setNombre($c["nombre"]);
            $contacto->setTelefono($c["telefono"]);
            $contacto->setEmail($c["email"]);
            $entityManager->persist($contacto);
        } try {
            $entityManager->flush();
            return new Response("Contactos insertados");
        } catch (\Exception $e) {
            return new Response("Ha ocurrido un error.");
        }
    }

    #[Route('/contactos', name: 'lista_contactos')]
    public function index2(): Response
    {
        return $this->render('contacto/index2.html.twig', [
            'controller_name' => 'ContactoController',
            'contactos' => $this->contactos
        ]);
    }


}

