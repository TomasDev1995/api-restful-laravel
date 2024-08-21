<?php

// app/Services/Authentication/AuthenticationService.php

namespace App\Services\Authentication;

use App\Repositories\User\UserRepository;
use App\Services\Security\PasswordHasher;
use App\DTO\User\UserDTO;
use App\Jobs\Authentication\SendWelcomeEmail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationService
{
    /**
     * @var UserRepository $userRepository Instancia del repositorio de usuarios para interactuar con la base de datos.
     */
    protected $userRepository;

    /**
     * @var PasswordHasher $passwordHasher Servicio para manejar el hash y la verificación de contraseñas.
     */
    protected $passwordHasher;

    /**
     * Constructor de AuthenticationService.
     * 
     * @param UserRepository $userRepository Instancia de UserRepository.
     * @param PasswordHasher $passwordHasher Instancia de PasswordHasher.
     */
    public function __construct(UserRepository $userRepository, PasswordHasher $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Registra un nuevo usuario en el sistema.
     * 
     * @param UserDTO $userDTO Data Transfer Object con la información del usuario.
     * @return array|null|object El usuario creado o null en caso de error.
     */
    public function registerUser(UserDTO $userDTO)
    {
        try {
            $createdUser = $this->createUser($userDTO);
            $this->sendWelcomeNotification($this->convertBSONDocumentToUserModel($createdUser));
            return $createdUser;
        } catch (Exception $e) {
            return $this->handleRegistrationError($e);
        }
    }

    /**
     * Autentica un usuario utilizando sus credenciales.
     * 
     * @param UserDTO $userDTO Data Transfer Object con la información del usuario.
     * @return array Con el usuario autenticado y el token de acceso.
     */
    public function authenticateUser(UserDTO $userDTO)
    {
        try {
            $user = $this->findUserByEmail($userDTO);
            $this->verifyPassword($userDTO->password, $user['password']);
            return [
                "user" => $user,
                "accessToken" => $this->generateToken($userDTO),
            ];
        } catch (Exception $e) {
            return $this->handleLoginError($e);
        }
    }

    /**
     * Encuentra un usuario por su correo electrónico.
     * 
     * @param UserDTO $userDTO Data Transfer Object con la información del usuario.
     * @return BSONDocument|null El documento BSON del usuario o null si no se encuentra.
     * @throws Exception Si el usuario no es encontrado.
     */
    private function findUserByEmail(UserDTO $userDTO): ?BSONDocument
    {
        $user = $this->userRepository->findByEmail($userDTO->email);

        if (!$user) {
            Log::error("Usuario no encontrado: $userDTO->email");
            throw new Exception('Usuario no encontrado');
        }

        return $user;
    }

    /**
     * Verifica que la contraseña proporcionada coincida con la almacenada.
     * 
     * @param string $password Contraseña proporcionada por el usuario.
     * @param string $hashedPassword Contraseña almacenada en la base de datos.
     * @return bool True si la contraseña es correcta, de lo contrario lanza una excepción.
     * @throws Exception Si la contraseña es incorrecta.
     */
    private function verifyPassword(string $password, string $hashedPassword): bool
    {
        $validated = $this->passwordHasher->check($password, $hashedPassword);
        if (!$validated) {
            Log::error("Contraseña incorrecta.");
            throw new Exception('Contraseña incorrecta');
        }

        return $validated;
    }

    /**
     * Genera un token de acceso JWT para el usuario.
     * 
     * @param UserDTO $userDTO Data Transfer Object con la información del usuario.
     * @return string Token de acceso generado.
     * @throws Exception Si no se puede generar el token.
     */
    private function generateToken(UserDTO $userDTO): string
    {
        $credentials = ["email" => $userDTO->email, "password" => $userDTO->password];

        if (!$token = JWTAuth::attempt($credentials)) {
            Log::error("Error al generar token de acceso");
            throw new Exception("Error al generar token de acceso");
        }

        return $token;
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     * 
     * @param UserDTO $userDTO Data Transfer Object con la información del usuario.
     * @return array|null|object El usuario creado o null si falla la creación.
     */
    private function createUser(UserDTO $userDTO): null|array|object
    {
        return $this->userRepository->create($this->convertDTOToArray($userDTO));
    }

    /**
     * Convierte un UserDTO en un array asociativo para ser almacenado en la base de datos.
     * 
     * @param UserDTO $userDTO Data Transfer Object con la información del usuario.
     * @return array Array con los datos del usuario.
     */
    private function convertDTOToArray(UserDTO $userDTO): array
    {
        return [
            'name' => $userDTO->name,
            'email' => $userDTO->email,
            'password' => $this->passwordHasher->hash($userDTO->password),
            'phone' => $userDTO->phone,
            'address' => $userDTO->address,
            'date_of_birth' => $userDTO->date_of_birth,
            'profile_picture' => $userDTO->profile_picture,
            'bio' => $userDTO->bio,
            'created_at' => $userDTO->created_at,
            'updated_at' => $userDTO->updated_at,
            'token' => $userDTO->token,
        ];
    }

    /**
     * Maneja errores ocurridos durante el registro de un usuario.
     * 
     * @param Exception $e Excepción capturada.
     * @throws Exception Nueva excepción con un mensaje amigable al usuario.
     */
    private function handleRegistrationError(Exception $e): void
    {
        Log::error("Error al registrar el usuario: " . $e->getMessage());
        throw new Exception("No se pudo registrar el usuario, por favor intente nuevamente.", 0, $e);
    }

    /**
     * Maneja errores ocurridos durante la autenticación de un usuario.
     * 
     * @param Exception $e Excepción capturada.
     * @return array Array con el mensaje de error.
     * @throws Exception Nueva excepción con el mensaje de error.
     */
    private function handleLoginError(Exception $e): array
    {
        Log::error("Error de autenticación: " . $e->getMessage());
        throw new Exception($e->getMessage());
    }

    /**
     * Convierte un documento BSON de usuario en un modelo de usuario.
     * 
     * @param mixed $bsonDocumentUser Documento BSON del usuario.
     * @return User Instancia del modelo User.
     */
    private function convertBSONDocumentToUserModel($bsonDocumentUser): User
    {
        return User::find($bsonDocumentUser->_id);
    }

    /**
     * Envía una notificación de bienvenida al usuario registrado.
     * 
     * @param User $user Instancia del modelo User.
     * @return void
     */
    private function sendWelcomeNotification(User $user): void
    {
        SendWelcomeEmail::dispatch($user);
    }
}
