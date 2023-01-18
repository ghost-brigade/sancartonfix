<script setup>
import SearchForm from '../components/forms/SearchForm.vue';

</script>

<template>
    <div class="app-home_illustrations">
        <RouterLink to="/search/carton" class="app-home_illustration">
            <img src="../assets/images/carton.png" />
            <h2>Cartons</h2>
        </RouterLink>
        <RouterLink to="/search/banc" class="app-home_illustration">
            <img src="../assets/images/banc.png" />
            <h2>Bancs</h2>
        </RouterLink>
        <RouterLink to="/search/tente" class="app-home_illustration">
            <img src="../assets/images/tente.png" />
            <h2>Tentes</h2>
        </RouterLink>
    </div>

    <section>
        <SearchForm />
    </section>

    <section class="app-img_section">
        <img class="app-img_section_img" src="../assets/images/ville.png" />
        <div class="app-img_section_content">
            <h2>Le concept</h2>
            <p>
                Nous avons pour objectif de fournir à l'ensemble des SDFs un logement décent.<br /><br />
                Nous savons que les moyens disponibles par ces derniers ne sont pas grands mais qu'ils méritent d'avoir leur “AirBnb” afin de ne plus se sentir à l'écart de cette société.<br /><br />
                C'est dans cette optique qu'est né “SanCartonFix”, l'application qui te permet de trouver ton carton, ton banc, ta tente, dans l'objectif de ne pas dormir directement sur le béton.
            </p>
        </div>
    </section>
</template>
