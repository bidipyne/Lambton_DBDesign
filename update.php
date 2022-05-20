<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$productName = $quantityOrdered = $priceEach =$date= "";
$productName_err = $quantityOrdered_err = $priceEach_err =$date_err= "";
 
// Processing form data when form is submitted  
if(isset($_POST["orderNumber"]) && !empty($_POST["orderNumber"])&&isset($_POST["orderLineNumber"])&& !empty($_POST["orderLineNumber"])){
  
    // Get hidden input value
    $orderNumber = $_POST["orderNumber"];
    $orderLineNumber = $_POST["orderLineNumber"];
  
 // Validate Order Date
 $input_date = trim($_POST["date"]);
 $test_arr  = explode('-', $input_date);

 if(empty($input_date)){
     $date_err = "Please enter Order Date.";
 }  elseif(checkdate($test_arr[0], $test_arr[1], $test_arr[2])){
    $date_err = "Please enter a valid Date.";
 } 
 else{
     $date = $input_date;
 }
    // Validate Product name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $productName_err = "Please enter a product name.";
    } 
    //elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
      //  $productName_err = "Please enter a valid name.";
    //} 
    else{
        $productName = $input_name;
    }
    
   

     // Validate Quantity Ordered
     $input_quantityOrdered = trim($_POST["quantityOrdered"]);
     if(empty($input_quantityOrdered)){
         $quantityOrdered_err = "Please enter the Quantity Ordered";     
     } elseif(!ctype_digit($input_quantityOrdered)){
         $quantityOrdered_err = "Please enter a positive integer value.";
     } else{
         $quantityOrdered = $input_quantityOrdered;
     }
    // Validate priceEach
    $input_priceEach = trim($_POST["priceEach"]);
    if(empty($input_priceEach)){
        $priceEach_err = "Please enter the price each.";     
    }// elseif(!ctype_alnum($input_priceEach)){
       //// $priceEach_err = "Please enter a positive integer value.";
    //} 
    else{
        $priceEach = $input_priceEach;
    }
    
    // Check input errors before inserting in database
    if(empty($productName_err) && empty($quantityOrdered_err) && empty($priceEach_err)){
        // Prepare an update statement
      $sql = "UPDATE orders JOIN orderdetails ON orders.orderNumber= orderdetails.orderNumber 
      INNER JOIN products on products.productCode=orderdetails.productCode SET products.productName=?,
      orderdetails.quantityOrdered =?,orderdetails.priceEach=?,orders.orderDate=? 
      WHERE orders.orderNumber=? and orderdetails.orderLineNumber=?";
      
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssii",$param_productName,$param_quantityOrdered, $param_priceEach,$param_orderDate, $param_id,$param_orderLineNumber);
            
            // Set parameters
            $param_productName = $productName;
            $param_quantityOrdered = $quantityOrdered;
            $param_priceEach = $priceEach;
            $param_orderDate= date("Y-m-d", strtotime($date)); 
            $param_id = $orderNumber;
            $param_orderLineNumber = $orderLineNumber;
           
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }else {
            echo "Something's wrong with the query: " . mysqli_error($link);
        }
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["orderNumber"]) && !empty(trim($_GET["orderNumber"]))&&isset($_GET["orderLineNumber"]) && !empty(trim($_GET["orderLineNumber"]))){
        // Get URL parameter
        $orderNumber =  trim($_GET["orderNumber"]);
        $orderLineNumber =  trim($_GET["orderLineNumber"]);
        // Prepare a select statement
       
        $sql = "SELECT * from orders INNER JOIN orderdetails on orders.orderNumber=orderdetails.orderNumber INNER JOIN products on products.productCode=orderdetails.productCode where orders.orderNumber=? and orderdetails.orderLineNumber=?";
      
        
        if($stmt = mysqli_prepare($link, $sql)){
             
             
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ii", $param_id,$param_orderLineNumber);
           
            // Set parameters
            $param_id = $orderNumber;
              $param_orderLineNumber=$orderLineNumber;
           
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
                $fsk=mysqli_num_rows($result);
                if(mysqli_num_rows($result) >0){
                   
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                   
                  
                    // Retrieve individual field value
                    $test_date = $row["orderDate"];
                   // $date= date("d-m-Y", strtotime($test_date)); 
                    $date=$row["orderDate"];
                    //echo "Something's wrong with the query: " .$date ;
                    $productName = $row["productName"];
                    $quantityOrdered = $row["quantityOrdered"];
                    $priceEach = $row["priceEach"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    //echo "Something's wrong with the query: " .$fsk . mysqli_error($link) ;
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }else {
            echo "Something's wrong with the query: " . mysqli_error($link);
        }
        
        // Close statement
        
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Record</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                    <div class="form-group <?php echo (!empty($date_err)) ? 'has-error' : ''; ?>">
                            <label>Order Date</label>
                            <input type="date" name="date" class="form-control"  value="<?php echo $date; ?>">
                            <span class="help-block"><?php echo $date_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($productName_err)) ? 'has-error' : ''; ?>">
                            <label>Product Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $productName; ?>">
                            <span class="help-block"><?php echo $productName_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($quantityOrdered_err)) ? 'has-error' : ''; ?>">
                          <label>Price Each</label>
                            <input type="text" name="quantityOrdered" class="form-control" value="<?php echo $quantityOrdered; ?>">
                            <span class="help-block"><?php echo $quantityOrdered_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($priceEach_err)) ? 'has-error' : ''; ?>">
                            
                            <label>Quantity Ordered</label>
                            <input type="text" name="priceEach" class="form-control" value="<?php echo $priceEach; ?>">
                            <span class="help-block"><?php echo $priceEach_err;?></span>
                        </div>
                        <input type="hidden" name="orderNumber" value="<?php echo $orderNumber; ?>"/>
                        <input type="hidden" name="orderLineNumber" value="<?php echo $orderLineNumber; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>