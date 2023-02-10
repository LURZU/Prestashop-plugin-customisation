
    {foreach from=$customs item=custom}
        <div class="product-line-info product-line-info-secondary text-muted">
            <span class="label">{$custom.name}:</span>
            <span class="value">{$custom.value}</span>
        </div>
    {/foreach}

