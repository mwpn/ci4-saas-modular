<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;
use Modules\Auth\Application\Services\AuthService;
use Modules\Auth\Infrastructure\Repositories\UserRepository;
use App\Services\TenantContext;

class ApiController extends ResourceController
{
    protected AuthService $authService;
    protected $format = 'json';

    public function __construct()
    {
        $this->authService = new AuthService(new UserRepository());
    }

    /**
     * @OA\Get(
     *     path="/api/health",
     *     summary="Health check endpoint",
     *     tags={"System"},
     *     @OA\Response(
     *         response=200,
     *         description="System health status",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="System is healthy"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="timestamp", type="string", format="date-time"),
     *                 @OA\Property(property="version", type="string", example="1.0.0"),
     *                 @OA\Property(property="tenant", type="string", example="current-tenant")
     *             )
     *         )
     *     )
     * )
     */
    public function health(): ResponseInterface
    {
        $data = [
            'timestamp' => date('c'),
            'version' => '1.0.0',
            'tenant' => TenantContext::getTenantName() ?? 'unknown'
        ];

        return $this->respond([
            'success' => true,
            'message' => 'System is healthy',
            'data' => $data
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User"),
     *                 @OA\Property(property="token", type="string", example="jwt-token-here")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function login(): ResponseInterface
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        $data = $this->request->getJSON(true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $result = $this->authService->login($email, $password);

        if ($result['success']) {
            return $this->respond([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'user' => $result['user'],
                    'token' => 'jwt-token-here' // TODO: Implement JWT
                ]
            ]);
        }

        return $this->respond([
            'success' => false,
            'message' => $result['message']
        ], 401);
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get all users",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="total_pages", type="integer"),
     *                 @OA\Property(property="total_items", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function users(): ResponseInterface
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;

        $userRepository = new UserRepository();
        $result = $userRepository->getUsersWithPagination($perPage, $page);

        return $this->respond([
            'success' => true,
            'data' => $result['users'],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $result['pager']->getPageCount(),
                'total_items' => $result['pager']->getTotal()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/stats",
     *     summary="Get dashboard statistics",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_users", type="integer", example=150),
     *                 @OA\Property(property="active_users", type="integer", example=120),
     *                 @OA\Property(property="total_tenants", type="integer", example=5),
     *                 @OA\Property(property="revenue", type="number", format="float", example=12500.50)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function dashboardStats(): ResponseInterface
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $userRepository = new UserRepository();
        $tenantId = TenantContext::getTenantId();

        $totalUsers = $userRepository->model->where('tenant_id', $tenantId)->countAllResults();
        $activeUsers = $userRepository->model->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->countAllResults();

        $stats = [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_tenants' => 1,
            'revenue' => 0
        ];

        return $this->respond([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="User registration",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function register(): ResponseInterface
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 422);
        }

        $data = $this->request->getJSON(true);
        $result = $this->authService->register($data);

        if ($result['success']) {
            return $this->respond([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => $result['user']
            ], 201);
        }

        return $this->respond([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * @OA\Get(
     *     path="/api/tenants",
     *     summary="Get all tenants",
     *     tags={"Tenants"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of tenants",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Tenant"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function tenants(): ResponseInterface
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Check if user is admin
        $user = $this->authService->getCurrentUser();
        if ($user['role'] !== 'admin') {
            return $this->respond([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
        }

        $tenantModel = new \Modules\Core\Infrastructure\Models\TenantModel();
        $tenants = $tenantModel->findAll();

        return $this->respond([
            'success' => true,
            'data' => $tenants
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/tenants/{id}",
     *     summary="Get tenant by ID",
     *     tags={"Tenants"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Tenant ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tenant details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Tenant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tenant not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getTenant($id = null): ResponseInterface
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->respond([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $tenantModel = new \Modules\Core\Infrastructure\Models\TenantModel();
        $tenant = $tenantModel->find($id);

        if (!$tenant) {
            return $this->respond([
                'success' => false,
                'message' => 'Tenant not found'
            ], 404);
        }

        return $this->respond([
            'success' => true,
            'data' => $tenant
        ]);
    }
}
