import { Api } from "@/api/api";

class Users extends Api {
  constructor() {
    super();
    this.path = "/users";
    this.forgotPasswordPath = "/forgot_password/";
    this.validateAccountPath = "/validate_account";
  }

  async findAll(page = 1, itemsPerPage = 20, filters = [], orders = {}) {
    return await this.get(this.path, page, itemsPerPage, filters, orders);
  }

  async findOne(id) {
    return await this.get(`${this.path}/${id}`);
  }

  async create(data) {
    return await this.post(this.path, data, false);
  }

  async update(id, data, jsonFormat = true) {
    return await this.put(`${this.path}/${id}`, data, jsonFormat);
  }

  async remove(id) {
    return await this.delete(`${this.path}/${id}`);
  }

  async validateAccount(token) {
    return await this.get(`${this.validateAccountPath}/${token}`, null, null, {}, null, false);
  }

  /**
   * @param {"email": "value"} data
   * @returns 204
   */
  async forgotPassword(data) {
    return await this.post(`${this.forgotPasswordPath}`, data, false);
  }

  /**
   * @param {string} token
   * @param {"password": "value"} data
   * @returns 204
   *
   */
  async resetPassword(token, data) {
    return await this.post(`${this.forgotPasswordPath}${token}`, data);
  }

  async payementIntent(amount) {
    return await this.post(`${this.path}/payment-intent`, { balanceStripe: amount }, false);
  }

}

export { Users };
