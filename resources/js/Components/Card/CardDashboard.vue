<template lang="">
    <div :class="['w-full card lg:w-1/2', classSet]">
        <div class="card-body">
            <div class="">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    style="fill: rgba(0, 0, 0, 1); transform: ; msfilter: "
                >
                    <path
                        d="M20 2H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h3v3.766L13.277 18H20c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2zm0 14h-7.277L9 18.234V16H4V4h16v12z"
                    ></path>
                    <circle cx="15" cy="10" r="2"></circle>
                    <circle cx="9" cy="10" r="2"></circle>
                </svg>
            </div>
            <h2 class="card-title">{{ total }}</h2>
            <p class="font-semibold text-md">{{ title }}</p>
        </div>
    </div>
</template>
<script>
export default {
  name: "CardDashboard",
  props: {
    title: {
      type: String,
      default: "Card title",
    },
    total: {
      type: String,
      default: "Value",
    },
    classSet: {
      type: String,
      default: "",
    },
  },
};
</script>
<style lang=""></style>
