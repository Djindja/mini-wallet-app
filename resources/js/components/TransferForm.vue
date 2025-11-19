<template>
  <div style="background: white; padding: 20px;">
    <h2>Send Money</h2>

    <p>Balance: ${{ balance.toFixed(2) }}</p>

    <form @submit.prevent="sendMoney">
      <div style="margin-bottom: 15px;">
        <label>Recipient User ID:</label><br>
        <input v-model.number="form.receiver_id" type="number" required style="width: 100%; padding: 8px; background: #e8c468">
      </div>

      <div style="margin-bottom: 15px;">
        <label>Amount:</label><br>
        <input v-model.number="form.amount" type="number" step="0.01" required style="width: 100%; padding: 8px; background: #e8c468">
      </div>

      <p v-if="error" style="color: red;">{{ error }}</p>
      <p v-if="success" style="color: green;">{{ success }}</p>

      <button type="submit" :disabled="loading" style="width: 100%; padding: 10px; background: #667eea; color: white; border: none; cursor: pointer;">
        {{ loading ? 'Sending...' : 'Send Money' }}
      </button>
    </form>
  </div>
</template>

<script lang="js">
import axios from 'axios';

export default {
  data() {
    return {
      balance: 0,
      form: { receiver_id: null, amount: null },
      loading: false,
      error: null,
      success: null,
    };
  },

  mounted() {
    this.fetchBalance();
  },

  methods: {
    async fetchBalance() {
      try {
        const token = localStorage.getItem('authToken');
        const res = await axios.get('/api/transactions', {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.data.success) this.balance = res.data.data.current_balance;
      } catch (err) {
        console.error(err);
      }
    },

    async sendMoney() {
      this.loading = true;
      this.error = null;
      this.success = null;

      try {
        const token = localStorage.getItem('authToken');
        const res = await axios.post('/api/transactions', this.form, {
          headers: { 'Authorization': `Bearer ${token}` }
        });

        if (res.data.success) {
          this.success = 'Transfer completed!';
          this.balance = res.data.data.current_balance;
          this.form = { receiver_id: null, amount: null };
          window.dispatchEvent(new CustomEvent('transaction-completed'));
          setTimeout(() => this.success = null, 3000);
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Transfer failed';
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>