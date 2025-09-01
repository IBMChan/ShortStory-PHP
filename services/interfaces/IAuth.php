<!-- Interface: Provides abstraction without implementation.
Polymorphism: Different classes can implement IAuth with custom behavior but will still be compatible where IAuth is expected. -->
<?php
interface IAuth {
    public function login($username, $password);
    public function signup($username, $password, $email, $phone, $confirmPassword);
    public function logout();
}
?>