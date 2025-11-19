import { createApp } from 'vue';
import TransferForm from './components/TransferForm.vue';
import TransactionList from './components/TransactionList.vue';

const app = createApp({
    template: `
    <div style="max-width: 1200px; margin: 50px auto; padding: 20px;">
      <h1 style="text-align: center; color: white;">Mini Wallet</h1>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px;">
        <TransferForm />
        <TransactionList />
      </div>
    </div>
  `,
    components: { TransferForm, TransactionList }
});

app.mount('#app');