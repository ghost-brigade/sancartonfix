<script setup>
import HousingForm from '../../components/forms/HousingForm.vue';
</script>

<template>
    <section>
        <h1>Créer un logement</h1>

        <HousingForm :type="'create'"/>
    </section>
</template>