<?php  if(!defined('TVic')) exit('Direct file access not allowed.'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title> TV-Icarus <?php pgtitle() ?> </title>
  <meta charset="utf-8" />
  <meta name="description" content="Description Here" />
  <meta name="keywords" content="Keyword1,Keyword2,Keyword3" />
  <link rel="stylesheet" href="<?php base() ?>style.css" />
  <link rel="icon" type="image/png" href="<?php base() ?>images/favico.png" />
</head>
<body>
<div id="wrapper">
  <header class="round2 shadow3">
    <a href="<?php base() ?>"> 
    	<img src="<?php base() ?>images/tvtube.png" alt="" /> </a>
    <h1> <?php title() ?> </h1>
    <h2> <?php tagline() ?> </h2>
    <nav>
      <?php menu() ?>
    </nav>
  </header>
<div id="page" class="round shadow">
