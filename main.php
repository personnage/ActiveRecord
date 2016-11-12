<?php require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Personnage\ActiveRecord\Model;
use Personnage\ActiveRecord\BaseModel;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

class User extends Model
{
    protected $table = 'users';

    protected function getPdo()
    {
        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s',
            getenv('DB_CONNECTION'),
            getenv('DB_HOST'),
            getenv('DB_PORT'),
            getenv('DB_DATABASE')
        );

        $options = [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];

        return new PDO(
            $dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), $options
        );
    }
}

$user = new User([
    'name' => 'John Doe',
    'email' => 'jd@example.com'
]);

var_dump($user->id);
$user->save();
var_dump($user->id);

// // Find User by id (PK)
// $user = User::find(1);
// // Update user
// $user->name = 'Bill';
// $user->save();
