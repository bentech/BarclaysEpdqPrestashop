<p class="payment_module">
 <a href="javascript:$('#epdq_form').submit();" title="{l s='Pay by Credit/Debit card with Barclaycard Business' mod='epdq'}">
  <img src="{$module_template_dir}epdq.gif" alt="{l s='Pay by Credit/Debit card with Barclaycard Business' mod='epdq'}" />
  {l s='Pay by Credit/Debit card with Barclaycard Business' mod='epdq'}<br />
  {$epdqSupportedCardImages}
 </a>
</p>

<form action="{$epdqGatewayUrl}" method="post" id="epdq_form" class="hidden">
{foreach from=$epitems key=k item=v}
 <input type="hidden" name="{$k}" value="{$v}" />{/foreach}
 <input type="hidden" name="SHASIGN" value="{$SHASIGN}">
 <input type="submit" value="" id=submit2 name=submit2> </form>
</form>
