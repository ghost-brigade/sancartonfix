import { Api } from "@/api/api";

class Users extends Api {
  constructor() {
    super();
    this.path = "/users";
    this.forgotPasswordPath = "/forgot_password";
    this.validateAccountPath = "/validate_account";
  }

  async findAll(page = 1, itemsPerPage = 20, filters = [], orders = {}) {
    return await this.get(this.path, page, itemsPerPage, filters, orders);
  }

  async findOne(id) {
    return await this.get(`${this.path}/${id}`);
  }

  async create(data) {
    return await this.post(this.path, data);
  }

  async update(id, data) {
    return await this.put(`${this.path}/${id}`, data);
  }

  async delete(id) {
    return await this.delete(`${this.path}/${id}`);
  }

  async validateAccount(token) {
    return await this.get(`${this.validateAccountPath}/${token}`);
  }

  /**
   * @param {"email": "value"} data 
   * @returns 204
   */
  async forgotPassword(data) {
    return await this.post(`${this.forgotPasswordPath}`, data);
  }

  /**
   * @param {string} token
   * @param {"password": "value"} data
   * @returns 204
   * 
   */
  async resetPassword(token, data) {
    return await this.post(`${this.forgotPasswordPath}/${token}`, data);
  }


}

export { Users };
