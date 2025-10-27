@hasSection('title')
    <title>@yield('title') | Coopérative Carbone</title>
@else
    <title>Coopérative Carbone</title>
@endif
<link rel="icon" type="image/png" href="{{ asset('img/logos/cooperative-carbone/favicon.png') }}">
<meta property="og:title" content="Accès privé à la plateforme de gestion des projets de séquestration/réduction carbone accompagnés par la Coopérative Carbone La Rochelle.">
<meta name="author" content="Coopérative Carbone & Eliott Baylot">
<meta property="og:locale" content="fr_FR">
<meta name="description" content="Accès privé à la plateforme de gestion des projets de séquestration/réduction carbone accompagnés par la Coopérative Carbone La Rochelle.">
<meta property="og:description" content="Accès privé à la plateforme de gestion des projets de séquestration/réduction carbone accompagnés par la Coopérative Carbone La Rochelle.">
<meta property="og:url" content="{{ config('app.url') }}">
<meta property="og:site_name" content="Coopérative Carbone">
