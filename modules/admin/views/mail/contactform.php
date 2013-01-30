<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style type="text/css">
         th {font: bold 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;color: #4f6b72;border 1px solid #C1DAD7;letter-spacing: 2px;text-transform: uppercase;text-align: left;padding: 6px 6px 6px 12px;background: #CAE8EA;}
  		 td {border 1px solid #C1DAD7;padding: 6px 6px 6px 12px;color: #4f6b72;}	
		</style>
      </head>
    
	<?php
    $title = t('New Response from form:').' '.$form_name;
    ?>
    <body>
      <h1><? echo $title ?></h1>
	  <table border='1'>
	  <tr>
	  <?php
	  foreach($form_keys as $keys)
		{
		echo "<th>".$keys."</th>";
		}
	  ?>
	  </tr>
	  <tr>
	  <?php
	  foreach($form_values as $values)
		{
		echo "<td>".$values."</td>";
		}
	  ?>
	  </tr>  
	  </table>
    </body>
</html>