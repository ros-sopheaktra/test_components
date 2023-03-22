const products = Object.freeze([
    {
        name          : 'Water',
        alertQuantity : 80,
        productPoint  : 2,
    },
    {
        name          : 'Cake',
        alertQuantity : 5,
        productPoint  : 2,
    },
    {
        name          : 'IPhone',
        alertQuantity : 150,
        productPoint  : 10,
    },
    {
        name          : 'AirPod',
        alertQuantity : 100,
        productPoint  : 9.8,
    },
]);

/**
 * 
 */
function getProducts(){
    return products;
}
