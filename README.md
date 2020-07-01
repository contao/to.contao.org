# Contao Short Link Service (https://to.contao.org)

This repo contains short links related to the [Contao Open Source CMS](https://contao.org) project.

Please note that no pull requests with short links for extensions, commercial products or anything similar will
be accepted.

### Configuration

All short links have to be added to the `redirects.yaml` file.

The easiest redirect is a language-independent short link for a longer URL such as:

```yaml
docs: https://docs.contao.org
```

This will generate the `https://to.contao.org/docs` short link and redirect to you `https://docs.contao.org`.

If you want to have language-dependent redirects, you can configure them like so:

```yaml
partner:
    en: https://contao.org/en/contao-partners.html
    de: https://contao.org/de/contao-partner.html
    es: https://contao.org/es/partner-contao.html
    fr: https://contao.org/fr/partenaires-contao.html
```

In this case, the `Accept-Language` header of the visitor is going to be analyzed, and they are being redirected to the
best available option. Note that if there's no match, the first target is going to be selected. So your fallback should
always be listed first (most likely `en`).
This also works for extended language keys like `de-CH` in which case, `de` is going to be selected.

If you want to force a certain language, you can use `https://to.contao.org/partner&lang=fr`.



