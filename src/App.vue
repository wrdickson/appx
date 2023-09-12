<script setup>
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <div class="container">
    <div class="head">
      <nav>
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/test">Test</RouterLink>
      </nav>
    </div>
    <div class="main">
      <RouterView />
    </div>
  </div>
</template>

<style scoped>
.container {
  font-family: Verdana, Geneva, Tahoma, sans-serif;
}
.container {
  margin-left: 5px;
  margin-right: 5px;
}

.head a:link, a:visited {
  text-decoration: none;
  color: #fbfbfe;
  font-size: 18px;
  padding: 4px;
  margin-right: 3px;
  border: 1px solid #8b8b8b;
  border-radius: 4px;
}
.head a:hover {
  background-color: rgb(136, 141, 209);
}



.main {

}
</style>
