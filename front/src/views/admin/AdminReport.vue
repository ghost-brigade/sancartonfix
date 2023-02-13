<script setup>
import { inject, onMounted, reactive } from 'vue';
import { Report } from '@/api/report';
import { Renting } from '@/api/renting';
import { Users } from '@/api/users';
import { SECURITY_currentUser } from '../../providers/ProviderKeys';
import Swal from 'sweetalert2';

const { currentUser } = inject(SECURITY_currentUser);

if (!currentUser?.id) {
    $router.push('/login');
}
if (currentUser?.roles[0] !== 'ROLE_ADMIN') {
    $router.push('/');
}

const reports = reactive([]);
const reportApi = new Report();
const rentingApi = new Renting();
const userApi = new Users();
onMounted(async () => {
    const data = await reportApi.findAll();
    Object.assign(reports, data['hydra:member']);
});

const deleteUser = async (report) => {
    const rentingData = await rentingApi.findOne(report.renting.split('/').pop());
    
    const user = rentingData.client;
    const userId = user.split('/').pop();

    const removed = await userApi.update(userId, { roles: ['ROLE_DELETED'] });
    Swal.fire({
        title: 'Utilisateur supprimé',
        icon: 'success',
        confirmButtonText: 'Ok',
    });
    report.status = 'deleted';
    await reportApi.update(report.id, { status: 'deleted' });
};
</script>

<template>
    <section>
        <h1>Reports</h1>

        <ul>
            <template v-for="report in reports" :key="report.id">
                <li v-if="report.status !== 'deleted'">
                    <p>{{ report.content }}</p>
                    <p>{{ report.renting }}</p>
                    <button @click="() => deleteUser(report)">Supprimer l'utilisateur</button>
                </li>
            </template>
        </ul>
    </section>
</template>