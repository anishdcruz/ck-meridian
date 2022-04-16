<template>
    <div class="metric-card" ref="inner">
      <div class="metric-card-inner">
        <div class="metric-card-title">
          <router-link :to="`/${metric.resource}?filter_id=${metric.filter_id}`">
            <span>{{metric.card_label}}</span>
          </router-link>
          <div class="metric-card-ctrl">
          	<i class="metric-card-trash icon icon-edit"
          	  v-if="edit && metric.filter !== null"
          	  @click="$emit('edit')"></i>
          	 <i class="metric-card-trash icon icon-trash-b"
          	  v-if="edit"
          	  @click="$emit('remove')"></i>
          </div>
        </div>
        <canvas ref="chart"></canvas>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js'

export default {
  props: {
    metric: Object,
    edit: {
      type: Boolean,
      default: false
    }
  },
  mounted() {
    let ctx = this.$refs.chart
    let inner = this.$refs.inner

    let options = {
    	// animation: false,
    	legend: {
	        display: false
	    },
	    elements: {
            point:{
                radius: 0
            }
        },
        scales: {
                xAxes: [{
                    gridLines: {
                        display:false
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display:false
                    }
                }]
            }
    }

    let backgroundColor = [
      `rgba(${this.metric.color}, 0.2)`,
      `rgba(255, 99, 132, 0.2)`,
      `rgba(54, 162, 235, 0.2)`,
      `rgba(255, 206, 86, 0.2)`,
      `rgba(75, 192, 192, 0.2)`,
      `rgba(153, 102, 255, 0.2)`,
      `rgba(255, 159, 64, 0.2)`
    ]

    let borderColor = [
      `rgba(${this.metric.color}, 1)`,
      `rgba(255, 99, 132, 1)`,
      `rgba(54, 162, 235, 1)`,
      `rgba(255, 206, 86, 1)`,
      `rgba(75, 192, 192, 1)`,
      `rgba(153, 102, 255, 1)`,
      `rgba(255, 159, 64, 1)`
    ]

    if(this.metric.chart_type === 'pie' || this.metric.chart_type === 'doughnut') {
      options.scales = {}
      // ctx.width = Number(inner.clientWidth)
      ctx.height = Number(inner.clientHeight)
      // console.log('h:', inner.clientHeight)
      backgroundColor = [
        `rgba(255, 99, 132, 0.8)`,
        `rgba(54, 162, 235, 0.8)`,
        `rgba(255, 206, 86, 0.8)`,
        `rgba(75, 192, 192, 0.8)`,
        `rgba(153, 102, 255, 0.8)`,
        `rgba(255, 159, 64, 0.8)`
      ]

      borderColor = []
    } else {
    	ctx.height = Number(inner.clientHeight) * 0.4
    }

    var myChart = new Chart(ctx, {
        type: this.metric.chart_type,
        data: {
            labels: this.metric.values.labels,
            datasets: [{
                label: this.$t(this.metric.y_axis_field),
                data: this.metric.values.data,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
                borderWidth: 1
            }]
        },
        options: options
    });
  },
  methods: {
  }
}
</script>