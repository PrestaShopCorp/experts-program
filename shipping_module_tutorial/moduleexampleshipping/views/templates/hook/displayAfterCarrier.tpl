<h2>Cart product list saved to database:</h2>
<ol>
{foreach from=$products item=product}
<li>{$product.name}, Quatity: {$product.quantity} pcs</li>
{/foreach}
</ol>