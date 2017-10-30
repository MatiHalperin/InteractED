<?php 
class Categories{
	function GetCategories(){
		require "../include/connect.php";

		$sql = 'SELECT CategoryName,CategoryID FROM Categories WHERE Implemented = 1';
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) 
        {
            while ($row = $result->fetch_assoc()){
               echo '<a class="categoryOption waves-effect" id="'.$row['CategoryName'].'"><img src="../images/category/'.$row['CategoryID'].'.png" alt="'.$row['CategoryName'].'" height="56" width="56"></a>';
            }
        }
	}	
	function GetArticlesByCategories($category){
		require "../include/connect.php";
		$sql = 'SELECT DISTINCT *
				FROM articles
				INNER JOIN categories ON articles.CategoryID = categories.CategoryID WHERE categories.CategoryName = "' .$category. '";';

		$result = $conn->query($sql);

		if ($result->num_rows > 0) 
		{
		    while ($row = $result->fetch_assoc())
		        echo '<div class="col s12" id="article">
		                  <div class="card horizontal hoverable item" id="' . $row['PostID'] . '">
		                      <div class="card-image" style="width: 192px;">
		                          <img src="' . $row['Image'] . '">
		                      </div>
		                      <div class="card-stacked">
		                          <div class="card-content">
		                              <strong>' . $row['Title'] . '</strong>
		                              <p>' . $row['Creator'] . '</p>
		                          </div>
		                      </div>
		                  </div>
		              </div>';
		}
	}
}
?>