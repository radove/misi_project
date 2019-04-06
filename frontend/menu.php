<nav class="navbar navbar-expand-lg navbar-light">
  <a class="navbar-brand" href="index.php?dove=reset"><img src="images/logo.png" class="mx-auto" height="35px" alt="Dovestech" border="0"></a>
  <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item <?php if($dove =='home'){echo 'active';}?>">
        <a class="nav-link" href="index.php?dove=home">Home</a>
      </li>
      <li class="nav-item <?php if($dove =='search'){echo 'active';}?>">
        <a class="nav-link" href="index.php?dove=search">Search</a>
      </li>
      <li class="nav-item <?php if($dove =='import'){echo 'active';}?>">
        <a class="nav-link" href="index.php?dove=import">Import</a>
      </li>
    </ul>
  </div>
</nav>


