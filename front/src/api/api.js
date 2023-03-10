class Api {
  static url = import.meta.env.VITE_API_URL;

  async #fetchApi(url, method, data, jsonFormat = true) {
    const headers = {
      "Content-Type": "application/json",
      Accept: "application/ld+json",
    };

    const token = localStorage.getItem("token");
    if (token) {
      headers["Authorization"] = `Bearer ${token}`;
    }

    try {
      const response = await fetch(url.href, {
        method: method,
        headers: headers,
        body: data ? JSON.stringify(data) : null,
      });

      return await (jsonFormat ? response.json() : response);
    } catch (error) {
      console.error(error);
    }
  }

  #buildUrl(path, page = 1, itemsPerPage = 20, filters = [], orders = {}) {
    const url = new URL(Api.url + path);

    url.searchParams.set("page", page);
    url.searchParams.set("itemsPerPage", itemsPerPage);

    if (filters.length > 0) {
      filters.forEach((filter) => {
        const property =
          filters?.length > 1 ? `${filter.property}[]` : filter.property;
        url.searchParams.append(property, filter?.value);
      });
    }

    if (orders?.property && orders?.direction) {
      url.searchParams.append(`order[${orders?.property}]`, orders?.direction);
    }

    return url;
  }

  async get(path, page = 1, itemsPerPage = 20, filters = [], orders = {}, jsonFormat = true) {
    const url = this.#buildUrl(path, page, itemsPerPage, filters, orders);

    try {
      return await this.#fetchApi(url, "GET", false, jsonFormat);
    } catch (err) {
      throw new Error("Error while getting data");
    }
  }

  async post(path, data, jsonFormat = true) {
    const url = new URL(Api.url + path);
    try {
      return await this.#fetchApi(url, "POST", data, jsonFormat);
    } catch (err) {
      throw new Error("Error while sending data");
    }
  }

  async put(path, data, jsonFormat = true) {
    const url = new URL(Api.url + path);
    try {
      return await this.#fetchApi(url, "PUT", data, jsonFormat);
    } catch (err) {
      throw new Error("Error while modifying data");
    }
  }

  async delete(path, jsonFormat = true) {
    const url = new URL(Api.url + path);
    try {
      return await this.#fetchApi(url, "DELETE", null, jsonFormat);
    } catch (err) {
      throw new Error("Error while deleting data");
    }
  }
}

export { Api };
