export default {
  data(){
      return {
          currentPagePelaksanaan: 1,
          perPagePelaksanaan: 10,
          loading: false,
      }
  },
  methods: {
      nextPagePelaksanaan(){
          if(this.currentPagePelaksanaan < this.pagesPelaksanaan[this.pagesPelaksanaan.length - 1]){
              this.currentPagePelaksanaan++
          }
      },
      previousPagePelaksanaan(){
          if(this.currentPagePelaksanaan != 1){
              this.currentPagePelaksanaan--
          }
      },
      nowPagePelaksanaan(page){
          this.currentPagePelaksanaan = page
      },
  },
  computed: {
      renderPaginatePelaksanaan() {
          if(this.searchPelaksanaan != ''){
              return this.format_events.filter((item) => {
                  return Object.keys(item).some((key) => {
                      return String(item[key]).toLowerCase().includes(this.searchPelaksanaan.toLowerCase())
                  }
                  )
              }).slice(
                  this.perPagePelaksanaan * (this.currentPagePelaksanaan - 1),
                  this.perPagePelaksanaan * this.currentPagePelaksanaan
                  );
          }else{
              return this.format_events.slice(
                  this.perPagePelaksanaan * (this.currentPagePelaksanaan - 1),
                  this.perPagePelaksanaan * this.currentPagePelaksanaan
                );
          }
        },
      pagesPelaksanaan() {
          let totalPagesPelaksanaan = Math.ceil(this.format_events.length / this.perPagePelaksanaan)

          let pagesPelaksanaan = []

          const totalPageNumber = this.currentPagePelaksanaan + 4
          
          for (let i = 1; i <= totalPagesPelaksanaan; i++) {
              if(i <= totalPageNumber && pagesPelaksanaan.length < 5){
                  pagesPelaksanaan.push(i)
              }else{
                  pagesPelaksanaan.push('...')
                  pagesPelaksanaan.push(totalPagesPelaksanaan)
                  break
              }
          }
          if(this.currentPagePelaksanaan > 5 && this.currentPagePelaksanaan < totalPagesPelaksanaan){
              pagesPelaksanaan = [1, '...', this.currentPagePelaksanaan - 1, this.currentPagePelaksanaan, this.currentPagePelaksanaan + 1, '...', totalPagesPelaksanaan]
          }else if(this.currentPagePelaksanaan > 5 && this.currentPagePelaksanaan == totalPagesPelaksanaan){
              pagesPelaksanaan = [1, '...', this.currentPagePelaksanaan - 1, this.currentPagePelaksanaan]
          }

          return pagesPelaksanaan
      }
  },
  watch: {
      searchPelaksanaan(){
          this.currentPagePelaksanaan = 1
      }
  }
}