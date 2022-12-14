
/**
 * DECLARING VARIABLES
 * IMPORTANT: two variables are printed by PHP to JS variables on the HTML transfer and transfer-edit
 * These variables are: array_rates & defaultCurrency
 */

let valueFrom = document.getElementById('value-from');
let valueTo = document.getElementById('value-to');
let exchangeRate = document.getElementById('exchange-field');
let currencyFieldFrom = document.getElementById('currency-field-from');
let currencyFieldTo = document.getElementById('currency-field-to');

function maskValueToMoney(value, fractionDigits) {
    /**
     * MASK A VALUE TO MONEY CURRENCY BEFORE INSERTING IN A INPUT VALUE
     * This mask keeps values in the money mask standard
     * RETURN STRING
     */
    let resultToString = value.toFixed(fractionDigits);

    result = resultToString.replaceAll('.', ',');

    return result;
}

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

updateCurrency('currency-field-to', 'currency-code-to');
updateCurrency('currency-field-from', 'currency-code-from');

function updateExchangeRate() {
    /**
     * UPDATE EXCHANGE RATE WHEN A CURRENCY IS UPDATED
     * It only works if one of the currencies is the user's default currency
     * The array is generated in a session when logged in
     * The rates are updated when the users log in
     * The field value and dataset.rate are updated
     * RETURN VOID
     */
    let exchangeRateField = document.getElementById('exchange-field');
    let currencyFieldFrom = document.getElementById('currency-field-from');
    let currencyFieldTo = document.getElementById('currency-field-to');
    let currencyFieldFromValue = currencyFieldFrom.value.toUpperCase();
    let currencyFieldToValue = currencyFieldTo.value.toUpperCase();

    
    if(defaultCurrency != currencyFieldFromValue && defaultCurrency != currencyFieldToValue) {
        return; // default currency is not there -> display warning message.
    }

    if(currencyFieldFromValue == defaultCurrency) {
        let exchangeRate = array_rates[currencyFieldToValue];
        console.log(typeof exchangeRate);
        exchangeRateField.value = maskValueToMoney(exchangeRate, 2);
        exchangeRateField.dataset.rate = maskValueToMoney(exchangeRate, 4);
        calculateRate(valueFrom, valueTo);
        return;
    }

    if(currencyFieldToValue == defaultCurrency) {
        let exchangeRate = array_rates[currencyFieldFromValue];
        let convertExchangeRate = 1 / exchangeRate;
        exchangeRateField.value = maskValueToMoney(convertExchangeRate, 2);
        exchangeRateField.dataset.rate = maskValueToMoney(exchangeRate, 4);
        calculateRate(valueTo, valueFrom);
        return
    }

}

function invertCurrencyButton() {
    /* 
    * INVERT THE CURRENCIES ONCLICK
    * 
    * RETURN VOID
    */
    let valueFrom = document.getElementById('value-from');
    let valueTo = document.getElementById('value-to');
    let currencyFieldFrom = document.getElementById('currency-field-from').value;
    let currencyFieldTo = document.getElementById('currency-field-to').value;

    document.getElementById('currency-field-from').value = currencyFieldTo;
    document.getElementById('currency-field-to').value = currencyFieldFrom;
    document.getElementById('value-from').value = valueTo.value;
    document.getElementById('value-to').value = valueFrom.value;
    
    
    if(defaultCurrency == currencyFieldTo.toUpperCase()) {
        updateExchangeRate();
        updateCurrency('currency-field-to', 'currency-code-to');
        updateCurrency('currency-field-from', 'currency-code-from');
        calculateRate(valueFrom, valueTo);
        return
    }

    let invertedRate = 1 / stringToFloat(exchangeRate.dataset.rate);
    exchangeRate.value = maskValueToMoney(invertedRate, 2);
    exchangeRate.dataset.rate = maskValueToMoney(invertedRate, 4);
 
    updateCurrency('currency-field-to', 'currency-code-to');
    updateCurrency('currency-field-from', 'currency-code-from');
    calculateRate(valueFrom, valueTo);
}

function stringToFloat(fieldValue) {
    // RETURN FLOAT
    return parseFloat(fieldValue.replaceAll('.','').replace(',','.'));
}


function calculateRate(updatedFieldId, fieldToUpdateId) {

    /* 
    * CALCULATE THE EXCHANGE VALUES WHEN VALUE FROM/TO ARE UPDATED
    * Calculates the exchange rate when a value is modified.
    * Calculated using the Exchange rate field dataset.rate
    * Properties STRING informing the input ID.
    * RETURN STRING
    */

    updatedValue = stringToFloat(updatedFieldId.value); // float
    rateDataSet = stringToFloat(exchangeRate.dataset.rate); // float

    let result;

    if (updatedFieldId == valueTo) {
        result = updatedValue / rateDataSet; // Return num.
    } else {
        result = updatedValue * rateDataSet; // Return num.
    }

    fieldToUpdateId.value = maskMoney(maskValueToMoney(result, 2)); // Return string - Update the value of the field with the exchange result.
}

function updateDataSetRate() {
    exchangeRate.dataset.rate = exchangeRate.value;
}

exchangeRate.addEventListener(
    'input', (e) => {
        exchangeRate.dataset.rate = exchangeRate.value;
        calculateRate(valueFrom, valueTo);
    },
    false
)

valueFrom.addEventListener(
  "input", (e) => {
    calculateRate(valueFrom, valueTo);
  },
  false
)

valueTo.addEventListener(
  "input", (e) => {
    calculateRate(valueTo, valueFrom);  
  },
  false
)

currencyFieldFrom.addEventListener(
  "input", (e) => {
    updateExchangeRate();
  },
  false
)

currencyFieldTo.addEventListener(
  "input", (e) => {
    updateExchangeRate();
  },
  false
)

