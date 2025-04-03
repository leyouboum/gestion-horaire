<?php

//Chargement des fichier config pour les chemins et database pour la connexion à la bdd
require_once __DIR__ . '/../config.php';             
require_once __DIR__ . '/../../backend/config/database.php'; 

use app\Config\Database;

// Récupération de la variable $conn pour l'initialisation de la connexion à la bdd
$conn = Database::getConnection();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Système de gestion des horaires universitaires">
  <meta name="author" content="Nom de l'auteur">
  <title>Admin | Gestion des Horaires Universitaires</title>
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/sb-admin-2.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/fontawesome-free/css/all.min.css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,
    400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <style>
    .nav-item:hover {
      background-color: white !important;
    }
    .nav-item:hover .nav-link, 
    .nav-item:hover .nav-link i {
      color: black !important;
    }
  </style>
</head>
<body id="page-top">
<!-- Page Wrapper -->
<div id="wrapper">
