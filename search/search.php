<?php
function postInArray($arr, $post) {
  foreach ($arr as $p) {
    if($p["PostID"] == $post["PostID"])
      return true;
  }
  return false;
}

function getSimilar($word, $maxDistance = 1) {
    require "../include/connect.php";

    $words = array();

    $length = strlen($word);

    for ($i = 0; $i < $length; $i++) {
        $first_part = substr($word, 0, $i);
        $last_part = substr($word, $i + 1);
        if($word[$i] != '_') {
            if($word[$i] != ' ')
                $words[] = $first_part . '_' . $word[$i] . $last_part;
            $words[] = $first_part . $last_part;
        }
        $words[] = $first_part . '_' . $last_part;
    }

    $words[] = $word . '_';

    $sql = "SELECT DISTINCT A.PostID, A.Title, U.Name FROM Articles A
            INNER JOIN Users U ON A.CreatorID = U.UserCode
            INNER JOIN Tags T ON A.PostID = T.PostID
            WHERE A.Title LIKE '%" . $words[0] . "%' OR U.Name LIKE '%" . $words[0] . "%' OR T.TagName LIKE '%" . $words[0] . "%' OR A.Transcript LIKE '%" . $words[0] . "%'";

    for($i = 1; $i < count($words); $i++)
        $sql .= " OR A.Title LIKE '%" . $words[$i] . "%' OR U.Name LIKE '%" . $words[$i] . "%' OR T.TagName LIKE '%" . $words[$i] . "%' OR A.Transcript LIKE '%" . $words[$i] . "%'";
        
    $searchResult = $conn->query($sql);

    $result =  array();
    $foundResults = False;

    if($searchResult->num_rows > 0) {
        while($row = $searchResult->fetch_assoc())
            $result[] = $row;
        $foundResults = True;
    }

    if($maxDistance > 1) {
      $count = count($words);
      for($i = 0; $i < $count; $i++) {
          $similar = getSimilar($words[$i], $maxDistance - 1);
          if(!is_null($similar)) {
            $result = array_merge($result, $similar);
            $foundResults = True;
          }
      }
    }

    if($foundResults) {
      $uniqueResults = array();
      foreach ($result as $r) {
        if(!postInArray($uniqueResults, $r))
          $uniqueResults[] = $r;
      }
      return $uniqueResults;
    }
    else
      return NULL;
}

function searchArticles($query, $maxWords) {
    require "../include/connect.php";
    include "../post/functions.php";

    $sql = "SELECT DISTINCT A.PostID, A.Title, U.Name FROM Articles A
            INNER JOIN Users U ON A.CreatorID = U.UserCode
            INNER JOIN Tags T ON A.PostID = T.PostID
            WHERE MATCH(A.Title, A.Transcript, U.Name, T.TagName) AGAINST ('" . $query . "' IN BOOLEAN MODE)";

    $searchResult = $conn->query($sql);

    if($searchResult->num_rows > 0) {
        echo '<p class="results">' . $result->num_rows . ' resultados para "' . $query . '"</p>';

        while ($row = $searchResult->fetch_assoc()) {
            $Image = glob("../images/posts/" . $row['PostID'] . ".*");

            addHorizontalCard($row['PostID'], $Image[0], $row['Title'], $row['Name']);
        }
    }
    else {
        if (str_word_count($query) > $maxWords) {
            $pieces = explode(" ", $query);
            $cutSearch = '<p style="color: #808080; font-size: small;"><b>"' . $pieces[$maxWords] . '"</b> (y las palabras que le siguen) se ignoraron porque limitamos las consultas a ' . $maxWords . ' palabras.</p>';
            $query = implode(" ", array_splice($pieces, 0, $maxWords));
        }

        $disance = 2; // Cambiar esto hace que busque m??s, pero hace (3 * [largo de string]) ^ distancia queries
        $posts = getSimilar($query, 2);

        if($posts != NULL) {
            $count = count($posts);
            echo '<p class="results">' . $count . ' resultados para "' . $query . '"</p>';
            for($i = 0; $i < $count; $i++) {
                $currentPost = $posts[$i];

                $Image = glob("../images/posts/" . $currentPost['PostID'] . ".*");

                addHorizontalCard($currentPost['PostID'], $Image[0], $currentPost['Title'], $currentPost['Name']);
              }
        }
        else {
            echo '<div>
                      <p>No se han encontrado resultados para tu b??squeda</p>
                      <p>Sugerencias:</p>
                      <ul style="margin-left:1.3em;">
                          <li style="list-style-type: disc;">Aseg??rate de que todas las palabras est??n escritas correctamente.</li>
                          <li style="list-style-type: disc;">Prueba diferentes palabras clave.</li>
                          <li style="list-style-type: disc;">Prueba palabras clave m??s generales.</li>
                      </ul>
                  </div>';            
        }
    }
}
?>