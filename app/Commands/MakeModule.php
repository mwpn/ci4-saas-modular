<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MakeModule extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'App';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'make:module';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Generates a new module with complete Clean Architecture structure';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'make:module <ModuleName> [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'ModuleName' => 'The name of the module to create'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--with-migration' => 'Create migration files',
        '--with-seeder' => 'Create seeder files',
        '--with-tests' => 'Create test files',
        '--with-api' => 'Create API controller and routes',
        '--with-views' => 'Create view files',
        '--force' => 'Force overwrite existing files'
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $moduleName = $params[0] ?? null;
        
        if (empty($moduleName)) {
            CLI::error('Module name is required!');
            CLI::write('Usage: php spark make:module <ModuleName>');
            return;
        }

        $moduleName = ucfirst($moduleName);
        $modulePath = FCPATH . '../modules/' . $moduleName;
        
        if (is_dir($modulePath) && !CLI::getOption('force')) {
            CLI::error("Module '{$moduleName}' already exists!");
            CLI::write('Use --force to overwrite existing files.');
            return;
        }

        CLI::write("🚀 Creating module: {$moduleName}", 'green');
        CLI::write("📁 Module path: {$modulePath}", 'yellow');

        $this->createModuleStructure($moduleName, $modulePath);
        $this->createCoreFiles($moduleName, $modulePath);

        CLI::write("✅ Module '{$moduleName}' created successfully!", 'green');
        CLI::write("📚 Next steps:", 'yellow');
        CLI::write("1. Register module in app/Config/Modules.php");
        CLI::write("2. Add routes in modules/{$moduleName}/Routes.php");
        CLI::write("3. Run migrations: php spark migrate");
        CLI::write("4. Start developing your module!");
    }

    private function createModuleStructure($moduleName, $modulePath)
    {
        $directories = [
            'Application/Services',
            'Domain/Entities',
            'Infrastructure/Models',
            'Infrastructure/Repositories',
            'Presentation/Controllers',
            'Presentation/Views',
            'Database/Migrations',
            'Database/Seeds',
            'Providers'
        ];

        foreach ($directories as $dir) {
            $fullPath = $modulePath . '/' . $dir;
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
                CLI::write("📁 Created: {$dir}", 'cyan');
            }
        }
    }

    private function createCoreFiles($moduleName, $modulePath)
    {
        // Service Provider
        $serviceProviderContent = "<?php

namespace Modules\\{$moduleName}\\Providers;

use CodeIgniter\\ServiceProvider;

class ServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register services here
    }

    public function boot()
    {
        // Boot services here
    }
}";

        file_put_contents($modulePath . '/Providers/ServiceProvider.php', $serviceProviderContent);
        CLI::write("📄 Created: ServiceProvider.php", 'cyan');

        // Routes
        $moduleLower = strtolower($moduleName);
        $routesContent = "<?php

use CodeIgniter\\Router\\RouteCollection;

\$routes->group('{$moduleLower}', function (\$routes) {
    \$routes->get('/', '{$moduleName}\\Presentation\\Controllers\\{$moduleName}Controller::index');
    \$routes->get('create', '{$moduleName}\\Presentation\\Controllers\\{$moduleName}Controller::create');
    \$routes->post('store', '{$moduleName}\\Presentation\\Controllers\\{$moduleName}Controller::store');
    \$routes->get('edit/(:num)', '{$moduleName}\\Presentation\\Controllers\\{$moduleName}Controller::edit/\$1');
    \$routes->post('update/(:num)', '{$moduleName}\\Presentation\\Controllers\\{$moduleName}Controller::update/\$1');
    \$routes->get('delete/(:num)', '{$moduleName}\\Presentation\\Controllers\\{$moduleName}Controller::delete/\$1');
});";

        file_put_contents($modulePath . '/Routes.php', $routesContent);
        CLI::write("📄 Created: Routes.php", 'cyan');

        // Entity
        $entityContent = "<?php

namespace Modules\\{$moduleName}\\Domain\\Entities;

class {$moduleName}Entity
{
    protected \$id;
    protected \$name;
    protected \$description;
    protected \$created_at;
    protected \$updated_at;

    public function __construct(\$data = [])
    {
        foreach (\$data as \$key => \$value) {
            if (property_exists(\$this, \$key)) {
                \$this->{\$key} = \$value;
            }
        }
    }

    public function getId() { return \$this->id; }
    public function getName() { return \$this->name; }
    public function setName(\$name) { \$this->name = \$name; return \$this; }
    public function getDescription() { return \$this->description; }
    public function setDescription(\$description) { \$this->description = \$description; return \$this; }

    public function toArray()
    {
        return [
            'id' => \$this->id,
            'name' => \$this->name,
            'description' => \$this->description,
            'created_at' => \$this->created_at,
            'updated_at' => \$this->updated_at,
        ];
    }
}";

        file_put_contents($modulePath . '/Domain/Entities/' . $moduleName . 'Entity.php', $entityContent);
        CLI::write("📄 Created: {$moduleName}Entity.php", 'cyan');

        // Model
        $tableName = strtolower($moduleName) . 's';
        $modelContent = "<?php

namespace Modules\\{$moduleName}\\Infrastructure\\Models;

use CodeIgniter\\Model;

class {$moduleName}Model extends Model
{
    protected \$table = '{$tableName}';
    protected \$primaryKey = 'id';
    protected \$useAutoIncrement = true;
    protected \$returnType = 'array';
    protected \$useSoftDeletes = false;
    protected \$protectFields = true;
    protected \$allowedFields = ['name', 'description'];

    protected \$useTimestamps = true;
    protected \$dateFormat = 'datetime';
    protected \$createdField = 'created_at';
    protected \$updatedField = 'updated_at';

    protected \$validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'description' => 'permit_empty|max_length[500]'
    ];
}";

        file_put_contents($modulePath . '/Infrastructure/Models/' . $moduleName . 'Model.php', $modelContent);
        CLI::write("📄 Created: {$moduleName}Model.php", 'cyan');

        // Controller
        $controllerContent = "<?php

namespace Modules\\{$moduleName}\\Presentation\\Controllers;

use CodeIgniter\\Controller;
use Modules\\{$moduleName}\\Infrastructure\\Models\\{$moduleName}Model;

class {$moduleName}Controller extends Controller
{
    protected {$moduleName}Model \$model;

    public function __construct()
    {
        \$this->model = new {$moduleName}Model();
    }

    public function index()
    {
        \$data['items'] = \$this->model->findAll();
        return view('Modules\\{$moduleName}\\Presentation\\Views\\index', \$data);
    }

    public function create()
    {
        return view('Modules\\{$moduleName}\\Presentation\\Views\\create');
    }

    public function store()
    {
        \$rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!\$this->validate(\$rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', \$this->validator->getErrors());
        }

        \$data = [
            'name' => \$this->request->getPost('name'),
            'description' => \$this->request->getPost('description')
        ];

        if (\$this->model->insert(\$data)) {
            return redirect()->to('/{$moduleName}')
                ->with('success', 'Item created successfully!');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create item');
    }
}";

        file_put_contents($modulePath . '/Presentation/Controllers/' . $moduleName . 'Controller.php', $controllerContent);
        CLI::write("📄 Created: {$moduleName}Controller.php", 'cyan');

        // View
        $viewContent = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$moduleName} Management</title>
    <script src=\"https://cdn.tailwindcss.com\"></script>
</head>
<body class=\"bg-gray-100\">
    <div class=\"container mx-auto px-4 py-8\">
        <div class=\"bg-white rounded-lg shadow-md p-6\">
            <h1 class=\"text-2xl font-bold text-gray-800 mb-6\">{$moduleName} Management</h1>
            
            <div class=\"mb-4\">
                <a href=\"/{$moduleName}/create\" class=\"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded\">
                    Add New
                </a>
            </div>

            <div class=\"overflow-x-auto\">
                <table class=\"min-w-full table-auto\">
                    <thead>
                        <tr class=\"bg-gray-50\">
                            <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase\">ID</th>
                            <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase\">Name</th>
                            <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase\">Description</th>
                            <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase\">Actions</th>
                        </tr>
                    </thead>
                    <tbody class=\"bg-white divide-y divide-gray-200\">
                        <?php foreach (\$items as \$item): ?>
                        <tr>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\"><?= \$item['id'] ?></td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\"><?= \$item['name'] ?></td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-500\"><?= \$item['description'] ?></td>
                            <td class=\"px-6 py-4 whitespace-nowrap text-sm font-medium\">
                                <a href=\"/{$moduleName}/edit/<?= \$item['id'] ?>\" class=\"text-indigo-600 hover:text-indigo-900 mr-3\">Edit</a>
                                <a href=\"/{$moduleName}/delete/<?= \$item['id'] ?>\" class=\"text-red-600 hover:text-red-900\">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>";

        file_put_contents($modulePath . '/Presentation/Views/index.php', $viewContent);
        CLI::write("📄 Created: index.php view", 'cyan');
    }
}
