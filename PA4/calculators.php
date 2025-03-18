<?php
session_start(); 
include("header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculators</title>
        <script src="js/calculator.js" defer></script>
</head>
<body>
    <!-- Include banner somewhere -->
    <div class="banner1">
        <img src="img/green_t.png" width="200" height="150" alt="comany name">
        <h1>McAlister's Listing</h1>
    </div>

    

    <main>
        <form id="mortgage_calculator">
            <label for="loan_amount">Loan Amount:</label>
            <input type="number" id="loan_amount" placeholder="Enter the loan amount" required name="interest_rate">

            <label for="interest_rate">Interest Rate (%):</label>
            <input type="number" id="interest_rate" value="11.75" readonly required>

            <label for="payment_num">Number of Payments:</label>
            <input type="number" id="payment_num" placeholder="Enter the loan term" required>

            <button type="button" id="mortgage_button">Calculate Monthly Mortgage Payment</button>
        </form>

        <form id="down_payment">
            <label for="sale_price">Price of Property:</label>
            <input type="number" id="sale_price" placeholder="Enter price of desired property" required name="sale_price">

            <label for="payment_percentage">Payment Percentage(%):</label>
            <input type="number" id="payment_percentage" placeholder="Enter the payment percentage" required>

            <button type="button" id="down_payment_button">Calculate Down Payment</button>
        </form>

        <div id="result"></div>
    </main>
    <?php
include("footer.php");
?>
</body>
</html>



