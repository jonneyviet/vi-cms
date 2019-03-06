<template>
    <div>
        <div :class="$style.top">
          <div :class="$style.logo">Vi-cms</div>
          <div></div>
        </div>
    </div>
</template>
<style lang="scss" scoped module>
    .top {
		height: 48px;
        background-color: #D32F2F;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding:0 10px;
    }
</style>