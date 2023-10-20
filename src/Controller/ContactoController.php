<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contactos;
use App\Entity\Provincia;
use LDAP\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/contacto/nuevo', name: 'nuevocontacto')]
    public function nuevo(ManagerRegistry $doctrine, Request $request) {
        $contacto = new Contactos();

        $formulario = $this->createFormBuilder($contacto)
            ->add('nombre', TextType::class)
            ->add('telefono', TextType::class)
            ->add('email', EmailType::class, array('label' => 'Correo electrónico'))
            ->add('provincia', EntityType::class, array(
                'class' => Provincia::class,
                'choice_label' => 'nombre',))
            ->add('save', SubmitType::class, array('label' => 'Enviar'))
            ->getForm();
            $formulario->handleRequest($request);

            if ($formulario->isSubmitted() && $formulario->isValid()) {
                $contacto = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($contacto);
                $entityManager->flush();
                return $this->redirectToRoute('ficha_contacto', ["codigo" => $contacto->getId()]);
            }


        return $this->render('contacto/nuevo.html.twig', array(
            'formulario' => $formulario->createView()
        ));

    }

    #[Route('/contacto/editar/{codigo}', name: 'editarcontacto')]
    public function editar(ManagerRegistry $doctrine, Request $request, $codigo) {

        $repositorio = $doctrine->getRepository(Contactos::class);
        $contacto = $repositorio->find($codigo);


        $formulario = $this->createFormBuilder($contacto)
            ->add('nombre', TextType::class)
            ->add('telefono', TextType::class)
            ->add('email', EmailType::class, array('label' => 'Correo electrónico'))
            ->add('provincia', EntityType::class, array(
                'class' => Provincia::class,
                'choice_label' => 'nombre',))
            ->add('save', SubmitType::class, array('label' => 'Enviar'))
            ->getForm();
            $formulario->handleRequest($request);

            if ($formulario->isSubmitted() && $formulario->isValid()) {
                $contacto = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($contacto);
                $entityManager->flush();
            }


        return $this->render('contacto/editar.html.twig', array(
            'formulario' => $formulario->createView()
        ));

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

