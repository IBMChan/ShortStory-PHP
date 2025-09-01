<?php
class User {
    private $username;
    private $email;
    private $contact;

    public function __construct($username, $email, $contact = null) {
        $this->username = $username;
        $this->email = $email;
        $this->contact = $contact;
    }

    public static function fromRow(array $row) {
        return new self(
            $row['u_name'] ?? '',
            $row['email'] ?? '',
            $row['contact'] ?? null
        );
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getContact() {
        return $this->contact;
    }

    public function setContact($contact) {
        $this->contact = $contact;
    }
}
