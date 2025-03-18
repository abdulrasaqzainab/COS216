//U22566202
//ZAINAB ABDULRASAQ

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//calculator
function calculateMortgagePayment() {
   
    var loanamount=parseFloat(document.getElementById("loan_amount").value);
    var interestrate=11.75;
    var numpayments=parseFloat(document.getElementById("payment_num").value);

    var monthlyinterest=interestrate/(12*100);

    var monthlymortgage=loanamount*monthlyinterest/(1-Math.pow(1+monthlyinterest,-numpayments));


    document.getElementById("result").textContent = "Mortgage Payment: R"+ monthlymortgage.toFixed(2) +" per month";
}
document.getElementById("mortgage_button").addEventListener("click",calculateMortgagePayment);


function downpayment() {
    
   
    var propertyprice=parseFloat(document.getElementById("monthly-income-afford").value);

    var paymentpercentage=parseFloat(document.getElementById("payment_percentage").value);

    var downpaymentval=(propertyprice*paymentpercentage)/100;
    document.getElementById("result").innerText = "Down Payment: R" +downpaymentval.toFixed(2)+ " loan amount";
}

document.getElementById("down_payment_button").addEventListener("click",downpayment);


/////////////////////////////////////
