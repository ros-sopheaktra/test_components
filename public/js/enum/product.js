const products = Object.freeze([
    {
        id             : 1, 
        name           : 'Product1',
        alert_quantity : 3,
        product_point  : 5,
    },
    {
        id             : 2, 
        name           : 'Product2',
        alert_quantity : 2,
        product_point  : 8,
    },
    {
        id             : 3, 
        name           : 'Product3',
        alert_quantity : 6,
        product_point  : 5,
    },
    {
        id             : 4, 
        name           : 'Product4',
        alert_quantity : 8,
        product_point  : 1,
    },
]);

/**
 * 
 */
function getProducts(){
    return products;
}
