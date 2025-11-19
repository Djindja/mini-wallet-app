<template>
  <div style="background: white; padding: 20px; border-radius: 8px;">
    <h3>Transaction History</h3>

    <p v-if="transactions.length === 0">No transactions yet</p>

    <div v-else>
      <div v-for="tx in transactions" :key="tx.id"
           style="padding: 10px; margin-bottom: 10px; background: #f5f5f5; border-radius: 4px;">
        <div style="display: flex; justify-content: space-between;">
          <div>
            <strong>{{ tx.type }}</strong><br>
            <small>{{ tx.direction === 'sent' ? 'To: ' + tx.receiver?.name : 'From: ' + tx.sender?.name }}</small>
          </div>
          <div :style="{ color: tx.direction === 'sent' ? 'red' : 'green', fontWeight: 'bold' }">
            {{ tx.direction === 'sent' ? '-' : '+' }}${{ parseFloat(tx.amount).toFixed(2) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      transactions: [],
    };
  },

  mounted() {
    this.fetchTransactions();
    window.addEventListener('transaction-completed', this.fetchTransactions);
  },

  methods: {
    async fetchTransactions() {
      try {
        const token = localStorage.getItem('authToken');
        const userId = parseInt(localStorage.getItem('userId'));
        const res = await axios.get('/api/transactions', {
          headers: { 'Authorization': `Bearer ${token}` }
        });

        if (res.data.success) {
          this.transactions = res.data.data.transactions.map(t => ({
            ...t,
            direction: t.sender_id === userId ? 'sent' : 'received'
          }));
        }
      } catch (err) {
        console.error(err);
      }
    },
  },
};
</script>