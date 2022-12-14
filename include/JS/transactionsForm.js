/**
 * SCRIPTS USED IN ANY TRANSACTION FORMS (INCLUE AND EDIT TRANSACTIONS)
 * 
 */

 function updateCurrency(inputField, currencyCodeInput) {
    /* 
    * UPDATE THE CURRENCY CODE WHEN ANOTHER CURRENCY IS SELECTED
    * Update the currency code (e.g USD, EUR) on the span field before the input value.
    * RETURN STRING
    */
    let selectedCurrency = document.getElementById(inputField).value;
    let currencyCodefield = document.getElementById(currencyCodeInput);
    currencyCodefield.innerHTML = selectedCurrency.toUpperCase();
}

/**
 * MASK MONEY TO INPUT FIELDS
 */
 const $money = document.querySelectorAll('[data-js="money"]');

 $money.forEach(item =>  { 
     item.addEventListener(
   "input", (e) => {
     e.target.value = maskMoney(e.target.value);
   }),
   false
 });
 
 function maskMoney(value) {
   const valueAsNumber = value.replace(/\D+/g, "");
   return new Intl.NumberFormat("pt-BR", {
     style: 'decimal', 
     minimumFractionDigits: 2, 
     maximumFractionDigits: 2
   }).format(valueAsNumber / 100);
 }