const currencies = Object.freeze([
    {
        label: 'euros',
        value: 'EUR',
        extraAttributes: {
            id: '1',
            symbol: '€',
            country: 'france',
        }
    },
    {
        label: 'dollars',
        value: 'USD',
        extraAttributes: {
            id: '2',
            symbol: '$',
            country: 'usd',
        }
    },
    {
        label: 'yen',
        value: 'JPY',
        extraAttributes: {
            id: '3',
            symbol: '¥',
            country: 'japan',
        }
    },
]);

/**
 * 
 */
function getCurrencies(){
    return currencies;
}