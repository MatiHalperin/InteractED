<nav>
    <div class="nav-wrapper white">
        <a href="/InteractED" class="logo blue-text">InteractED</a>
        <form id="search-form" action="/InteractED/search" method="GET" class="hide-on-small-only">
            <div class="input-field">
                <div class="hide-on-med-and-up">
                    <i id="search-mobile-back" class="material-icons prefix grey-text toggle-search">arrow_back</i>
                </div>
                <input id="search" name="q" type="search" placeholder="Buscar en InteractED" class="autocomplete">
                <div class="hide-on-small-only">
                    <label class="label-icon" for="search"><i class="material-icons" id="search-icon">search</i></label>
                </div>
                <i class="material-icons waves-effect waves-light" id="search-close-icon">close</i>
            </div>
        </form>
        <ul id="right-buttons" class="right">
            <?php
            if (isset($_SESSION["UserCode"]))
                echo '<li><i id="search-mobile-icon" class="material-icons grey-text hide-on-med-and-up toggle-search">search</i></li>
                          <li><a href="/InteractED/post/create"><i class="material-icons grey-text option">add</i></a></li>
                          <li><a href="/InteractED/category"><i id="category-icon" class="material-icons grey-text">view_column</i></a></li>
                          <li><img id="profile-picture" class="dropdown-button circle option" src="' . $_SESSION["Image"] . '" data-belowOrigin="true" data-constrainWidth="false" data-activates="account"></li>

                          <ul id="account" class="dropdown-content">
                              <div class="valign-wrapper grey lighten-3">
                                  <img id="dropdown-profile-picture" class="circle" src="' . $_SESSION["Image"] . '">
                                  <p id="dropdown-user-info" class="black-text"><strong>' . $_SESSION["Name"] . '</strong><br>' . $_SESSION["Email"] . '</p>
                              </div>
                              <li><a href="/InteractED/account" class="black-text"><i class="material-icons grey-text">account_circle</i>Mi cuenta</a></li>
                              <li><a href="/InteractED/post/my-posts" class="black-text"><i class="material-icons grey-text">content_paste</i>Mis articulos</a></li>
                              <li><a href="/InteractED/chats" class="black-text"><i class="material-icons grey-text">chat</i>Mis chats</a></li>
                              <li><a href="/InteractED/history" class="black-text"><i class="material-icons grey-text">query_builder</i>Historial</a></li>
                              <li><a href="/InteractED/login/logout.php" class="black-text"><i class="material-icons grey-text">exit_to_app</i>Salir</a></li>
                          </ul>

                          <ul id="notifications" class="dropdown-content notification-box">
                              <div class="valign-wrapper grey lighten-3">
                                  <h2 class="black-text notification-box-title">Notificaciones</h2>
                                  <i class="material-icons notification-box-icon">settings</i>
                              </div>
                              <li>
                                  <div class="notification-wrapper">
                                      <img class="circle notification-user" src="https://lh5.googleusercontent.com/-5nektcI4MAY/AAAAAAAAAAI/AAAAAAAAAAA/7JwddinvPqI/s96-mo/photo.jpg">
                                      <p class="notification-text">Carga reciente recomendada: La fuerte discusi??n entre Toranzo y Zubeld??a en Racing</p>
                                      <img class="notification-image" src="https://i.ytimg.com/vi/PCyytKlCrIk/hqdefault.jpg">
                                      <i class="material-icons notification-options">more_vert</i>
                                  </div>
                              </li>
                          </ul>';
            else
                echo '<li><a href="/InteractED/login" id="login" class="btn-flat blue-text waves-effect">Acceder</a></li>';
            ?>
        </ul>
    </div>
</nav>