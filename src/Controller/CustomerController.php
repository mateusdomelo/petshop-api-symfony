<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    const CUSTOMERS_ENDPOINT = '/customers';
    const CUSTOMERS_SINGLE_ENDPOINT = '/customers/{customerId}';

    #[Route(self::CUSTOMERS_ENDPOINT, name: 'customers_list', methods: 'GET')]
    public function get(CustomerRepository $repository): JsonResponse
    {
        $result = $repository->findAll();
        return $this->json($result);
    }

    #[Route(self::CUSTOMERS_SINGLE_ENDPOINT, name: 'customers_single', methods: 'GET')]
    public function getSingle(int $customerId, CustomerRepository $repository): JsonResponse
    {
        $customer = $repository->find($customerId) ?? throw $this->createNotFoundException('Customer not found');
        return $this->json($customer);
    }

    #[Route(self::CUSTOMERS_ENDPOINT, name: 'customers_create', methods: 'POST')]
    public function create(Request $request, CustomerRepository $repository): JsonResponse
    {
        $data = $request->request->all() ?:
            throw new InvalidArgumentException('It can\'t proceed: Data is empty');
        $curDate = new DateTimeImmutable('now', new DateTimeZone('-3'));
        $customer = new Customer();
        $customer->setName($data['name'])
            ->setLastname($data['lastname'])
            ->setTelephone($data['telephone'])
            ->setEmail($data['email'])
            ->setIdDocument($data['id_document'])
            ->setLastLogin(null)
            ->setCreatedAt($curDate)
            ->setUpdatedAt($curDate);
        $repository->save($customer, true);

        return $this->json([
            'message' => 'Customer created successfully',
        ], 201);
    }

    #[Route(self::CUSTOMERS_SINGLE_ENDPOINT, name: 'customers_update', methods: ['PUT', 'PATCH'])]
    public function update(int $customerId, Request $request, CustomerRepository $repository): JsonResponse
    {
        $customer = $repository->find($customerId) ?? throw $this->createNotFoundException('Customer not found');
        $data = $request->request->all();
        $curDate = new DateTimeImmutable('now', new DateTimeZone('-3'));
        $customer->setName($data['name'])
            ->setLastname($data['lastname'])
            ->setTelephone($data['telephone'])
            ->setEmail($data['email'])
            ->setIdDocument($data['id_document'])
            ->setUpdatedAt($curDate);
        $repository->save($customer, true);

        return $this->json([
            'message' => 'Customer updated successfully',
            'data' => $customer
        ]);
    }

    #[Route(self::CUSTOMERS_SINGLE_ENDPOINT, name: 'customers_delete', methods: 'DELETE')]
    public function delete(int $customerId, CustomerRepository $repository): JsonResponse
    {
        $customer = $repository->find($customerId) ?? throw $this->createNotFoundException('Customer not deleted');
        $repository->remove($customer, true);

        return $this->json([
            'message' => 'Customer deleted successfully',
            'data' => $customer
        ]);
    }
}
