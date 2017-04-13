{extends "$layout"}

{block name="content"}
  <section>
    <h3>{l s='There was an error with your payment.'}</h3>
    <pre>{$error}</pre>
    <p>{l s="Please contact the website administrator for more information."}</p>
  </section>
{/block}
