import Alpine from 'alpinejs'

// Import Alpine.js plugins
import intersect from '@alpinejs/intersect'
import persist from '@alpinejs/persist'

// Register Alpine.js plugins
Alpine.plugin(intersect)
Alpine.plugin(persist)

// Make Alpine available globally
window.Alpine = Alpine

// Start Alpine
Alpine.start()

// Import Chart.js for statistics
import Chart from 'chart.js/auto'

// Make Chart available globally
window.Chart = Chart

// Import Sortable.js for drag and drop
import Sortable from 'sortablejs'

// Make Sortable available globally
window.Sortable = Sortable

// Custom Alpine.js components
Alpine.data('qrCodeForm', () => ({
  qrType: 'url',
  content: {},
  design: {
    foregroundColor: '#000000',
    backgroundColor: '#ffffff',
    eyeColor: '#000000',
    logo: null,
    sticker: null,
    format: 'square',
    resolution: 300
  },
  
  init() {
    this.updateContent()
  },
  
  updateContent() {
    this.content = this.getDefaultContent(this.qrType)
  },
  
  getDefaultContent(type) {
    const defaults = {
      url: { url: '' },
      vcard: { 
        firstName: '', 
        lastName: '', 
        organization: '', 
        title: '', 
        phone: '', 
        email: '', 
        website: '' 
      },
      business: { 
        name: '', 
        description: '', 
        phone: '', 
        email: '', 
        website: '', 
        address: '' 
      },
      coupon: { 
        title: '', 
        description: '', 
        discount: '', 
        validUntil: '' 
      },
      text: { text: '' },
      email: { 
        to: '', 
        subject: '', 
        body: '' 
      },
      phone: { number: '' },
      sms: { 
        number: '', 
        message: '' 
      },
      wifi: { 
        ssid: '', 
        password: '', 
        security: 'WPA' 
      },
      location: { 
        latitude: '', 
        longitude: '', 
        address: '' 
      }
    }
    
    return defaults[type] || {}
  }
}))

Alpine.data('dashboard', () => ({
  stats: {
    totalQrCodes: 0,
    totalScans: 0,
    uniqueScans: 0,
    todayScans: 0
  },
  
  init() {
    this.loadStats()
  },
  
  async loadStats() {
    try {
      const response = await fetch('/api/dashboard/stats')
      this.stats = await response.json()
    } catch (error) {
      console.error('Error loading stats:', error)
    }
  }
}))

Alpine.data('qrCodePreview', () => ({
  qrCodeUrl: '',
  loading: false,
  
  async generatePreview(content, design) {
    this.loading = true
    
    try {
      const response = await fetch('/api/qr-codes/preview', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ content, design })
      })
      
      const data = await response.json()
      this.qrCodeUrl = data.url
    } catch (error) {
      console.error('Error generating preview:', error)
    } finally {
      this.loading = false
    }
  }
}))