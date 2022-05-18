<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Order Details</h2>
                        <a href="create.php" class="btn btn-success pull-right">Add New Order</a>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    // Attempt select query execution
                    $sql = "SELECT 
                    orderNumber,
                    orderDate,
                    orderLineNumber,
                    productName,
                    quantityOrdered,
                    priceEach
                FROM
                    orders
                INNER JOIN
                    orderdetails USING (orderNumber)
                INNER JOIN
                    products USING (productCode)
                ORDER BY 
                    orderNumber, 
                    orderLineNumber;
                ";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Order Number</th>";
                                        echo "<th>Order Date</th>";
                                        echo "<th>Order LineNumber</th>";
                                        echo "<th>Product Number</th>";
                                        echo "<th>Quantity Ordered</th>";
                                        echo "<th>Price Each</th>";
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['orderNumber'] . "</td>";
                                        echo "<td>" . $row['orderDate'] . "</td>";
                                        echo "<td>" . $row['orderLineNumber'] . "</td>";
                                        echo "<td>" . $row['productName'] . "</td>";
                                        echo "<td>" . $row['quantityOrdered'] . "</td>";
                                        echo "<td>" . $row['priceEach'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='read.php?orderNumber=". $row['orderNumber'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<a href='update.php?orderNumber=". $row['orderNumber'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='delete.php?orderNumber=". $row['orderNumber'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
 
                    // Close connection
                    mysqli_close($link);
                    ?>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>