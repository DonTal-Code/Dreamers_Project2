<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\UserManager;
use App\Model\OrderManager;

class SecurityController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function register()
    {
        $userManager = new UserManager();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (empty($_POST['username'])) {
                $errors['username'] = "Votre pseudo est requis";
            }

            if (empty($_POST['firstname'])) {
                $errors['firstname'] = "Votre prénom est requis";
            }

            if (empty($_POST['lastname'])) {
                $errors['lastname'] = "Votre nom est requis";
            }

            if (empty($_POST['email'])) {
                $errors['email'] = "Votre email est requis";
            }

            if (empty($_POST['password'])) {
                $errors['password'] = "Un mot de passe est requis";
            }

            if (empty($_POST['check_password'])) {
                $errors['check_password'] = "Veuillez entrer une nouvelle fois votre mot de passe";
            }

            if (empty($_POST['birthday'])) {
                $errors['birthday'] = "Votre date de naissance est requise";
            }

            if (empty($_POST['adress'])) {
                $errors['adress'] = "Votre adresse est requise";
            }

            if (empty($_POST['phone'])) {
                $errors['phone'] = "Votre numéro de téléphone est requis";
            }
            if (
                !empty($_POST['username'])
                && !empty($_POST['firstname'])
                && !empty($_POST['lastname'])
                && !empty($_POST['email'])
                && !empty($_POST['password'])
                && !empty($_POST['check_password'])
                && !empty($_POST['birthday'])
                && !empty($_POST['adress'])
                && !empty($_POST['phone'])
            ) {
                $user = $userManager->searchUser($_POST['username']);
                if (!$user) {
                    if ($_POST['password'] === $_POST['check_password']) {
                        if (strlen($_POST['password']) >= 6 && strlen($_POST['password']) <= 10) {
                            $user = [
                                'username' => $_POST['username'],
                                'firstname' => $_POST['firstname'],
                                'lastname' => $_POST['lastname'],
                                'email' => $_POST['email'],
                                'password' => md5($_POST['password']),
                                'birthday' => $_POST['birthday'],
                                'adress' => $_POST['adress'],
                                'phone' => $_POST['phone'],
                                'is_admin' => 0,
                            ];
                            $id = $userManager->insert($user);
                            if ($id) {
                                $_SESSION['user'] = $userManager->selectOneById($id);
                                header('Location: /');
                            }
                        } else {
                            $errors['password1'] = "Le mot de passe doit contenir entre 6 et 10 caractères!";
                        }
                    } else {
                        $errors['password1'] = "Les mots de passe ne correspondent pas !";
                    }
                } else {
                    $errors['username1'] = "Ce pseudo existe déjà.";
                }
            }
        }
        return $this->twig->render('Security/register.html.twig', [
            'user' => $userManager->selectAll(), 'errors' => $errors
        ]);
    }

    public function login()
    {
        $userManager = new UserManager();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (empty($_POST['username'])) {
                $errors['username'] = "Votre pseudo est requis";
            }

            if (empty($_POST['password'])) {
                $errors['password'] = "Un mot de passe est requis";
            }
            if (!empty($_POST['username']) && !empty($_POST['password'])) {
                $user = $userManager->searchUser($_POST['username']);
                if ($user) {
                    if ($user['password'] === md5($_POST['password'])) {
                        if ($user['is_admin'] === '1') {
                            $_SESSION['user'] = $user;
                            header('Location: /');
                        } else {
                            $_SESSION['user'] = $user;
                            header('Location: /');
                        }
                    } else {
                        $errors['password1'] = "Mot de passe invalide";
                    }
                } else {
                    $errors['username1'] = "Ce pseudo n'existe pas !";
                }
            }
        }
        return $this->twig->render('Security/login.html.twig', [
            'user' => $userManager->selectAll(), 'errors' => $errors
        ]);
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
    }

    public function show()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /');
        }
        $orderManager = new OrderManager();
        $orders = $orderManager->getOrdersByUser($_SESSION['user']['id']);
        return $this->twig->render('Security/show.html.twig', ['orders' => $orders]);
    }

    public function edit(int $id)
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 0) {
            $errors = [];
            $userManager = new UserManager();
            $user = $userManager->selectOneById($id);

            if ($_SERVER['REQUEST_METHOD'] === "POST") {
                $errors = [];
                if (empty($_POST['username'])) {
                    $errors['username'] = "Votre pseudo est requis";
                }
                if (empty($_POST['firstname'])) {
                    $errors['firstname'] = "Votre prénom est requis";
                }
                if (empty($_POST['lastname'])) {
                    $errors['lastname'] = "Votre nom est requis";
                }
                if (empty($_POST['email'])) {
                    $errors['email'] = "Votre email est requis";
                }
                if (empty($_POST['password'])) {
                    $errors['password'] = "Un mot de passe est requis";
                }
                if (empty($_POST['check_password'])) {
                    $errors['check_password'] = "Veuillez entrer une nouvelle fois votre mot de passe";
                }
                if (empty($_POST['birthday'])) {
                    $errors['birthday'] = "Votre date de naissance est requise";
                }
                if (empty($_POST['adress'])) {
                    $errors['adress'] = "Votre adresse est requise";
                }
                if (empty($_POST['phone'])) {
                    $errors['phone'] = "Votre numéro de téléphone est requis";
                }
                if (
                    !empty($_POST['username'])
                    && !empty($_POST['firstname'])
                    && !empty($_POST['lastname'])
                    && !empty($_POST['email'])
                    && !empty($_POST['password'])
                    && !empty($_POST['check_password'])
                    && !empty($_POST['birthday'])
                    && !empty($_POST['adress'])
                    && !empty($_POST['phone'])
                ) {
                    if ($_POST['password'] === $_POST['check_password']) {
                        if (strlen($_POST['password']) >= 6 && strlen($_POST['password']) <= 10) {
                            $user['username'] = $_POST['username'];
                            $user['firstname'] = $_POST['firstname'];
                            $user['lastname'] = $_POST['lastname'];
                            $user['email'] = $_POST['email'];
                            $user['password'] = md5($_POST['password']);
                            $user['birthday'] = $_POST['birthday'];
                            $user['adress'] = $_POST['adress'];
                            $user['phone'] = $_POST['phone'];
                            $user['is_admin'] = 0;
                            $user = $userManager->update($user);
                            unset($_SESSION['user']);
                            $_SESSION['user'] = $userManager->selectOneById($id);
                            header('Location: /security/show');
                        } else {
                            $errors['password1'] = "Le mot de passe doit contenir entre 6 et 10 caractères!";
                        }
                    } else {
                        $errors['password1'] = "Les mots de passe ne correspondent pas !";
                    }
                } else {
                    $errors['username1'] = "Ce pseudo existe déjà.";
                }
            }
            return $this->twig->render('Security/edit.html.twig', ['user' => $user, 'errors' => $errors]);
        } else {
            header('Location: /');
        }
    }
}
