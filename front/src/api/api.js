class Api {
  static url = "https://localhost";

  async #fetchApi(url, method, data = null) {
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

      return await response;
    } catch (error) {
      console.error(error);
    }
  }

  #buildUrl({path, page = 1, itemsPerPage = 20, filters = [], orders = {}}) {
    const url = new URL(Api.url + path);

    if (page) {
      url.searchParams.append("page", page);
    }

    if (itemsPerPage) {
      url.searchParams.set("itemsPerPage", itemsPerPage);
    }

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

  async get({path, page = null, itemsPerPage = null, filters = [], orders = {} }) {
    const url = this.#buildUrl({ path, page, itemsPerPage, filters, orders });

    try {
      return await this.#fetchApi(url, "GET");
    } catch (err) {
      throw new Error("Error while getting data");
    }
  }

  async post(path, data) {
    const url = new URL(Api.url + path);
    try {
      return await this.#fetchApi(url, "POST", data);
    } catch (err) {
      throw new Error("Error while sending data");
    }
  }

  async put(path, data) {
    const url = new URL(Api.url + path);
    try {
      return await this.#fetchApi(url, "PUT", data);
    } catch (err) {
      throw new Error("Error while modifying data");
    }
  }

  async delete(path) {
    const url = new URL(Api.url + path);
    try {
      return await this.#fetchApi(url, "DELETE");
    } catch (err) {
      throw new Error("Error while deleting data");
    }
  }
}

export { Api };
