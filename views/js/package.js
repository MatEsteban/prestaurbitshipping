/**
 * Object construct and validate package
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 */

var Package = function(){
    // construct demension of product
    this.product_width;
    this.product_height;
    this.product_length;
    this.product_weight;
    // constructs demension of package
    this.package_width;
    this.package_height;
    this.package_length;
    this.package_margin;
    this.max_dimension = 105;
    this.max_cubic = 0.25;
    this.max_weight = 22;//kg
    /**
     * function validate
     * rule of urbit:
     * +> max demension = 105 cm
     * +> max cubic = 0.25 m3
     * rule logic dimensions of product <= dimension of package
     * @return int
     * 1: case: dimension & product weight > dimension package & product default;
     * 2: case: product does not fit package 
     * 3: case: package's cubic is not validated
     * 0: valid OR nothing is entered
     */
    this.isValidate = function()
    {
        if (
                (typeof this.product_length !== 'undefined' && parseInt(this.product_length) > this.max_dimension) || 
                (typeof this.product_height !== 'undefined' && parseInt(this.product_height) > this.max_dimension) ||
                (typeof this.product_width !== 'undefined' && parseInt(this.product_width) > this.max_dimension) ||
                (typeof this.package_width !== 'undefined' && parseInt(this.package_width) > this.max_dimension) ||
                (typeof this.package_height !== 'undefined' && parseInt(this.package_height) > this.max_dimension) ||
                (typeof this.package_length !== 'undefined' && parseInt(this.package_length) > this.max_dimension)
        )
        {
           return 1;
        }
        
        var arrayProduct = [parseInt(this.product_width),parseInt(this.product_height),parseInt(this.product_length)];
        var arrayPackage = [parseInt(this.package_width),parseInt(this.package_height),parseInt(this.package_length)];
        arrayProductsort = arrayProduct.sort(function(a,b){
            return a-b;
        });
        arrayPackagesort = arrayPackage.sort(function(a,b){
            return a-b;
        });
        for(var i = 0; i < arrayProductsort.length; i++)
        {
            if (arrayProductsort[i] > arrayPackagesort[i])
            {
                return 2;
            }
        }
        if (
                
                typeof this.package_width !== 'undefined' && 
                typeof this.package_height !== 'undefined' && 
                typeof this.package_length !== 'undefined'
        )
        {
            // Cubic is in m3. Dimension is in cm. That's why the result is divided by 1000.000
            cubic = ( (parseInt(this.package_width) + parseInt(this.package_margin)) 
					 * (parseInt(this.package_height) + parseInt(this.package_margin)) 
					 * (parseInt(this.package_length) + parseInt(this.package_margin)))/1000000;
            if (cubic > this.max_cubic)
            {
                return 3;
            }
        }
	if ((!this.isValidateProductWeight()))
	{
	    return 4;
	}
        return 0;
    };
    this.isValidateProductWeight = function()
    {
	$flag = true;
	if (typeof this.product_weight !== 'undefined')
	{
	    switch (this.weight_unit)
	    {
		case 'kg':
		    if (parseFloat(this.product_weight) > parseFloat(this.max_weight))
		    {
			$flag = false;
		    }
		    break;
		case 'gr':
		    if (parseFloat(this.product_weight) > parseFloat(this.max_weight) * 1000)
		    {
			$flag = false;
		    }
		    break;
		default :
		    // default ton 1 tan = 1000 kg
		    if (parseFloat(this.product_weight) > parseFloat(this.max_weight) * 0.001)
		    {
			$flag = false;
		    }
		    break;
	    }
	}
	return $flag;
    };
    

};



