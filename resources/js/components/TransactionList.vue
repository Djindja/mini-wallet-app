<template>
  <div style="background: white; padding: 20px;">
    <h3>Transaction History</h3>

    <p v-if="transactions.length === 0">No transactions yet</p>

    <div v-else>
      <div v-for="tx in transactions" :key="tx.id"
           style="padding: 10px; margin-bottom: 10px; background: #f5f5f5;">
        <div style="display: flex; justify-content: space-between;">
          <div>
            <strong>{{ tx.type }}</strong><br>
            <small>{{ tx.direction === 'incoming' ? 'To: ' + tx.receiver?.name + ' from ' + tx.sender?.name : 'From: ' + tx.sender?.name + ' to ' + tx.receiver?.name }}</small>
          </div>
          <div :style="{ color: tx.direction === 'incoming' ? 'red' : 'green' }">
            {{ tx.direction === 'incoming' ? '-' : '+' }}${{ parseFloat(tx.amount).toFixed(2) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
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