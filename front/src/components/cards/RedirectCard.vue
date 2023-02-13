<script setup>
defineProps({
    key: {
        type: String,
        required: false,
    },
    redirect: {
        type: String,
        required: false,
        default: null,
    },
});
</script>

<template>
    <li class="app-card" :key="key">
        <RouterLink :to="redirect">
            <div class="app-card_img">
                <slot name="image"></slot>
            </div>

            <div class="app-card_content">
                <slot></slot>
            </div>

        </RouterLink>
    </li>
</template>
