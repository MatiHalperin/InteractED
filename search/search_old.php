<?php
function SearchQuery($Search, $MaxWords) {
    require "../include/connect.php";
    $sql = 'SELECT DISTINCT A.* FROM Articles A
            INNER JOIN EditorRelation ER ON A.PostID = ER.PostID
            INNER JOIN Users U ON U.UserCode = ER.UserCode
            WHERE A.Title LIKE "%' . $Search . '%" OR U.User LIKE "%' . $Search . '%" OR A.Tags LIKE "%' . $Search . '%" OR A.Transcript LIKE "%'. $Search . '%"';

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<p class="results">' . $result->num_rows . ' resultados para "' . $Search . '"</p>';

        while($row = $result->fetch_assoc())
            AddResult($row['PostID'], $row['Image'], $row['Title'], $row['Creator']);
    }
    else {
        if (str_word_count($Search) > $MaxWords) {
            $Pieces = explode(" ", $Search);
            $CutSearch = '<p style="color: #808080; font-size: small;"><b>"' . $Pieces[$MaxWords] . '"</b> (y las palabras que le siguen) se ignoraron porque limitamos las consultas a ' . $MaxWords . ' palabras.</p>';
            $Search = implode(" ", array_splice($Pieces, 0, $MaxWords));
        }

        $Words = CheckQuery($Search);

        // En la base de datos ya no se guarda el nombre del creador

        $sql = 'SELECT DISTINCT A.* FROM Articles A
                INNER JOIN EditorRelation ER ON A.PostID = ER.PostID
                INNER JOIN Users U ON U.UserCode = ER.UserCode
                WHERE A.Title LIKE "%' . $Words[0] . '%" OR U.User LIKE "%' . $Words[0] . '%" OR A.Tags LIKE "%' . $Words[0] . '%" OR A.Transcript LIKE "%'. $Words[0] . '%"';

        for ($i = 1; $i < count($Words); $i++)
            $sql .= 'OR A.Title LIKE "%' . $Words[$i] . '%" OR U.User LIKE "%' . $Words[$i] . '%" OR A.Tags LIKE "%' . $Words[$i] . '%" OR A.Transcript LIKE "%'. $Words[$i] . '%"';

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            if (isset($CutSearch))
                echo $CutSearch;
            echo '<p class="results">' . $result->num_rows . ' resultados</p>';

            while ($row = $result->fetch_assoc())  
            AddResult($row['PostID'], $row['Image'], $row['Title'], $row['Creator']);
        }
        else
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

    $conn->close();
}

function CheckQuery($Search) {
    return array_merge(
        Replace($Search, "Delete"),
        Replace($Search, "Replace"),
        Replace($Search, "Swap"),
        Replace($Search, "Insert")
    );
}

function Replace($Search, $Action) {
    $Alphabet = str_split("abcdefghijklmnopqrstuvwxyz0123456789");
    switch ($Action) {
        case "Delete":
            for ($i = 0; $i < $length; $i++)
                $Edits[] = substr($Search, 0, $i) . substr($Search, $i + 1);
            break;
        case "Replace":
            for ($i = 0; $i < $length; $i++)
                foreach ($Alphabet as $Letter)
                    $Edits[] = substr($Search, 0, $i) . $Letter . substr($Search, $i + 1);
            break;
        case "Swap":
            for ($i = 0; $i < $length; $i++) 
                $Edits[] = substr($Search, 0, $i) . $Search[$i + 1] . $Search[$i] . substr($Search, $i + 2);
            break;
        case "Insert":
            for ($i = 0; $i < $length; $i++) 
                foreach ($Alphabet as $Letter)
                    $Edits[] = substr($Search, 0, $i) . $Letter . substr($Search, $i);
            break;
    }
    return array_unique($Edits);
}
?>