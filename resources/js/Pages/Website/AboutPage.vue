<script setup>
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  import {
    faGlobeAmericas,
    faHeart,
    faUsers,
    faMountain,
    faCompass,
    faStar,
    faLightbulb,
    faArrowRight,
    faQuoteLeft,
    faCheckCircle,
    faGlobe,
  } from '@fortawesome/free-solid-svg-icons';
  import { computed } from 'vue';

  const props = defineProps({
    pageContent: Object,
    user: Object,
    isEditMode: Boolean,
    websiteSettings: Object,
  });

  // Icon mapping function
  const getIcon = (iconName) => {
    const iconMap = {
      faGlobeAmericas: faGlobeAmericas,
      faCompass: faCompass,
      faHeart: faHeart,
      faUsers: faUsers,
      faLightbulb: faLightbulb,
      faCheckCircle: faCheckCircle,
      faStar: faStar,
      faMountain: faMountain,
    };
    return iconMap[iconName] || faHeart;
  };

  // Computed properties for default values
  const heroStats = computed(() => {
    return (
      props.pageContent?.sections?.hero?.stats || [
        { number: '5K+', label: 'Team Members', description: 'Passionate Adventurers' },
        { number: '50+', label: 'Countries Reached', description: 'Global Community' },
        { number: '3+', label: 'Years of Passion', description: 'Dedicated Service' },
      ]
    );
  });

  const missionPoints = computed(() => {
    return (
      props.pageContent?.sections?.mission?.points || [
        'Born from a passion for exploration and storytelling, Adventure Log was created to bridge the gap between memorable experiences and lasting documentation.',
        "We understand that every journey, whether it's climbing mountains or exploring local hidden gems, deserves to be remembered and shared in a beautiful, meaningful way.",
        'Our platform combines intuitive design with powerful features to help you create stunning visual narratives of your adventures.',
      ]
    );
  });

  const featureCards = computed(() => {
    return (
      props.pageContent?.sections?.featureCards || [
        {
          title: 'Global Community',
          description:
            'Join adventurers from around the world sharing their incredible stories and inspiring others to explore.',
          icon: 'faGlobeAmericas',
        },
        {
          title: 'Innovative Platform',
          description:
            'Cutting-edge tools and features designed specifically for documenting and sharing your adventures beautifully.',
          icon: 'faCompass',
        },
        {
          title: 'Built with Passion',
          description:
            'Created by adventurers, for adventurers. We live and breathe exploration and understand your needs.',
          icon: 'faHeart',
        },
      ]
    );
  });

  const statsData = computed(() => {
    return (
      props.pageContent?.stats || {
        team_members: '5K+',
        countries_reached: '50+',
        years_of_passion: '3+',
      }
    );
  });

  const valuesItems = computed(() => {
    return (
      props.pageContent?.sections?.values?.items || [
        {
          title: 'Authenticity',
          description:
            'Real stories from real adventurers. We celebrate genuine experiences and authentic voices.',
          action: 'Stay True',
        },
        {
          title: 'Inspiration',
          description:
            "Spark curiosity and adventure in others. Every story has the power to inspire someone's next journey.",
          action: 'Light the Way',
        },
        {
          title: 'Community',
          description:
            'Connect with fellow explorers worldwide. Together, we create a global network of adventure seekers.',
          action: 'Grow Together',
        },
      ]
    );
  });
</script>

<template>
  <div class="overflow-hidden theme-page">
    <!-- ========== ABOUT PAGE HERO SECTION ========== -->
    <section
      class="relative bg-gradient-to-br from-secondary via-primary to-secondary text-white py-16 md:py-24 overflow-hidden"
    >
      <!-- Background Image with Enhanced Overlay -->
      <div class="absolute inset-0">
        <img
          src="https://images.unsplash.com/photo-1455849318743-b2233052fcff?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1169"
          alt="Mountain landscape"
          class="w-full h-full object-cover scale-105 animate-slow-zoom"
        />
        <!-- Multi-layered gradient overlays -->
        <div
          class="absolute inset-0 bg-gradient-to-br from-indigo-900/85 via-purple-800/50 to-teal-900/75"
        ></div>
        <div
          class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"
        ></div>
        <div
          class="absolute inset-0 bg-gradient-radial from-transparent via-transparent to-black/40"
        ></div>
      </div>

      <!-- Enhanced Animated Background Elements -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div
          class="absolute top-16 -left-20 w-96 h-96 bg-cyan-400/20 rounded-full blur-3xl animate-float-glow"
        ></div>
        <div
          class="absolute top-32 right-10 w-[500px] h-[500px] bg-purple-500/20 rounded-full blur-3xl animate-pulse-glow-slow"
        ></div>
        <div
          class="absolute bottom-10 left-1/4 w-80 h-80 bg-amber-400/15 rounded-full blur-3xl animate-float-delayed"
        ></div>
        <div
          class="absolute top-1/2 right-1/3 w-64 h-64 bg-emerald-400/15 rounded-full blur-3xl animate-pulse-glow delay-1000"
        ></div>
      </div>

      <!-- Floating Particles -->
      <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div
          class="absolute top-[10%] left-[15%] w-2 h-2 bg-white/40 rounded-full animate-float-particle"
        ></div>
        <div
          class="absolute top-[25%] left-[75%] w-1.5 h-1.5 bg-amber-300/50 rounded-full animate-float-particle delay-500"
        ></div>
        <div
          class="absolute top-[60%] left-[20%] w-2.5 h-2.5 bg-cyan-300/40 rounded-full animate-float-particle delay-1000"
        ></div>
        <div
          class="absolute top-[45%] left-[85%] w-1 h-1 bg-purple-300/50 rounded-full animate-float-particle delay-1500"
        ></div>
        <div
          class="absolute top-[80%] left-[50%] w-2 h-2 bg-emerald-300/40 rounded-full animate-float-particle delay-2000"
        ></div>
      </div>

      <!-- Floating Icons with Enhanced Animations -->
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/4 left-10 text-white/25 text-4xl animate-float-bounce">
          <FontAwesomeIcon :icon="faCompass" class="drop-shadow-glow" />
        </div>
        <div class="absolute top-1/3 right-16 text-white/20 text-5xl animate-float-spin-slow">
          <FontAwesomeIcon :icon="faMountain" class="drop-shadow-glow" />
        </div>
        <div class="absolute bottom-1/4 left-1/4 text-white/25 text-3xl animate-float-pulse">
          <FontAwesomeIcon :icon="faUsers" class="drop-shadow-glow" />
        </div>
        <div
          class="absolute top-1/2 right-1/4 text-white/15 text-6xl animate-float-drift delay-3000"
        >
          <FontAwesomeIcon :icon="faHeart" class="drop-shadow-glow" />
        </div>
        <div
          class="absolute bottom-1/3 right-10 text-white/20 text-2xl animate-float-bounce-delayed"
        >
          <FontAwesomeIcon :icon="faGlobe" class="drop-shadow-glow" />
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <!-- Premium Badge -->
        <div class="mb-8 inline-block animate-slide-down">
          <span
            class="glass-effect-premium text-white px-8 py-3.5 rounded-full text-sm font-bold backdrop-blur-xl border-2 border-white/40 inline-flex items-center space-x-3 shadow-2xl hover:scale-105 transition-transform duration-300 cursor-pointer group"
          >
            <FontAwesomeIcon :icon="faStar" class="text-yellow-400 animate-pulse-bright" />
            <span class="relative">
              {{ pageContent?.sections?.hero?.badge || 'Our Story & Mission' }}
              <span
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shine"
              ></span>
            </span>
            <FontAwesomeIcon
              :icon="faArrowRight"
              class="text-xs group-hover:translate-x-1 transition-transform"
            />
          </span>
        </div>

        <!-- Main Heading with Enhanced Typography -->
        <h1
          class="text-5xl md:text-6xl lg:text-7xl font-black mb-8 leading-tight animate-fade-scale-up"
        >
          <span
            class="block mb-4 drop-shadow-2xl hover:scale-105 transition-transform duration-300"
            style="
              text-shadow:
                0 0 40px rgba(255, 255, 255, 0.3),
                0 4px 20px rgba(0, 0, 0, 0.8);
            "
          >
            <span
              class="bg-gradient-to-r from-white via-blue-100 to-white bg-clip-text text-transparent"
            >
              {{ pageContent?.sections?.hero?.title || 'About Our' }}
            </span>
          </span>
          <span
            class="block bg-gradient-to-r from-amber-300 via-yellow-400 to-emerald-400 bg-clip-text text-transparent animate-gradient-x drop-shadow-lg text-5xl md:text-6xl"
            style="background-size: 200% 200%"
          >
            {{ pageContent?.sections?.hero?.highlightedTitle || 'Adventure Journey' }}
          </span>
        </h1>

        <!-- Enhanced Subtitle -->
        <p
          class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto leading-relaxed font-light animate-fade-in-up delay-200"
          style="text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7)"
        >
          {{
            pageContent?.sections?.hero?.subtitle ||
            'Discover the story behind Adventure Log and the passionate team dedicated to helping you document and share your journeys with the world.'
          }}
        </p>

        <!-- Enhanced Stats Section -->
        <div
          class="mt-16 grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-4xl mx-auto animate-fade-in-up delay-700"
        >
          <div
            v-for="(stat, index) in heroStats"
            :key="index"
            class="stat-card-premium glass-effect-card rounded-2xl p-6 text-center relative overflow-hidden group cursor-pointer transform hover:scale-105 transition-all duration-500 border-2 border-white/20 hover:border-amber-300/50 shadow-xl hover:shadow-2xl"
          >
            <div
              class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"
            ></div>
            <div class="text-4xl md:text-5xl font-black text-white mb-3 relative z-10">
              <span
                class="bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-500 bg-clip-text text-transparent drop-shadow-lg animate-pulse-subtle"
              >
                {{ stat.number }}
              </span>
            </div>
            <div class="text-white font-bold text-lg mb-2 relative z-10">{{ stat.label }}</div>
            <div class="text-white/70 text-sm relative z-10">{{ stat.description }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission & Story Section -->
    <section class="py-20 bg-gradient-to-b from-accent to-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
          <!-- Left: Story Content -->
          <div class="space-y-8">
            <div>
              <div
                class="inline-flex items-center space-x-2 bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-bold mb-6"
              >
                <FontAwesomeIcon :icon="faLightbulb" />
                <span>{{ pageContent?.sections?.mission?.title || 'OUR MISSION' }}</span>
              </div>
              <h2 class="text-4xl md:text-5xl font-black text-primary mb-6 leading-tight">
                {{ pageContent?.sections?.mission?.heading || 'Empowering Adventurers Worldwide' }}
              </h2>
            </div>

            <div class="space-y-6 text-gray-700 text-lg leading-relaxed">
              <p
                v-for="(point, index) in missionPoints"
                :key="index"
                class="flex items-start space-x-3"
              >
                <FontAwesomeIcon :icon="faCheckCircle" class="text-primary mt-1 flex-shrink-0" />
                <span>{{ point }}</span>
              </p>
            </div>

            <!-- Quote -->
            <div
              class="bg-gradient-to-br from-primary/5 to-secondary/5 border-l-4 border-primary p-6 rounded-r-2xl"
            >
              <FontAwesomeIcon :icon="faQuoteLeft" class="text-primary/30 text-3xl mb-4" />
              <p class="text-gray-700 text-lg italic leading-relaxed">
                {{
                  pageContent?.sections?.mission?.quote ||
                  "Every adventure is a story waiting to be told. We're here to help you tell yours in the most beautiful way possible."
                }}
              </p>
              <p class="text-primary font-bold mt-4">
                {{ pageContent?.sections?.mission?.quoteAuthor || '— The Adventure Log Team' }}
              </p>
            </div>
          </div>

          <!-- Right: Feature Cards -->
          <div class="space-y-6">
            <div
              v-for="(card, index) in featureCards"
              :key="index"
              class="group relative bg-white rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden hover:-translate-y-2"
            >
              <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary/10 to-transparent rounded-bl-full"
              ></div>
              <div class="relative">
                <div
                  class="inline-flex items-center justify-center w-16 h-16 bg-black rounded-2xl mb-6 text-accent text-3xl shadow-lg group-hover:scale-110 transition-transform"
                >
                  <FontAwesomeIcon :icon="getIcon(card.icon)" />
                </div>
                <h3
                  class="text-2xl font-bold text-primary mb-3 group-hover:text-secondary transition-colors"
                >
                  {{ card.title }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                  {{ card.description }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section with Modern Design -->
    <section class="py-20 bg-gradient-to-br from-primary to-secondary relative overflow-hidden">
      <!-- Decorative Background -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-20 w-64 h-64 bg-accent rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-80 h-80 bg-accent rounded-full blur-3xl"></div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-black text-accent mb-4">
            {{ pageContent?.sections?.statsSection?.title || 'Adventure by the Numbers' }}
          </h2>
          <p class="text-accent/80 text-xl">
            {{
              pageContent?.sections?.statsSection?.subtitle ||
              'Growing every day with passionate explorers'
            }}
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div
            v-for="(value, key) in statsData"
            :key="key"
            class="group text-center p-8 bg-accent/95 backdrop-blur-sm rounded-3xl shadow-2xl hover:shadow-3xl hover:scale-105 transition-all duration-300"
          >
            <div
              class="text-5xl md:text-6xl text-primary font-black mb-3 group-hover:scale-110 transition-transform"
            >
              {{ value }}
            </div>
            <div class="text-gray-700 font-bold text-lg capitalize">
              {{ key.replace('_', ' ') }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Core Values with Enhanced Cards -->
    <section class="py-24 bg-gradient-to-b from-gray-50 to-accent">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-20">
          <div class="inline-block mb-6">
            <span
              class="bg-primary/10 text-primary px-5 py-2 rounded-full text-sm font-bold inline-flex items-center space-x-2"
            >
              <FontAwesomeIcon :icon="faStar" />
              <span>{{ pageContent?.sections?.values?.badge || 'WHAT DRIVES US' }}</span>
            </span>
          </div>
          <h2 class="text-5xl md:text-6xl font-black text-primary mb-6 leading-tight">
            {{ pageContent?.sections?.values?.title || 'Our Core Values' }}
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            {{
              pageContent?.sections?.values?.subtitle ||
              'The principles that guide everything we do'
            }}
          </p>
        </div>

        <!-- Values Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
          <!-- Authenticity -->
          <div
            class="group relative bg-white p-10 rounded-3xl border-2 border-gray-100 hover:border-primary transition-all duration-500 hover:shadow-2xl hover:-translate-y-2"
          >
            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2">
              <div
                class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center text-white text-3xl shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all"
              >
                <FontAwesomeIcon :icon="faCheckCircle" />
              </div>
            </div>
            <div class="mt-8 text-center">
              <h3
                class="text-2xl font-bold text-primary mb-4 group-hover:text-secondary transition-colors"
              >
                {{ valuesItems[0]?.title || 'Authenticity' }}
              </h3>
              <p class="text-gray-600 text-lg leading-relaxed mb-4">
                {{
                  valuesItems[0]?.description ||
                  'Real stories from real adventurers. We celebrate genuine experiences and authentic voices.'
                }}
              </p>
              <div class="flex items-center justify-center space-x-2 text-primary font-semibold">
                <FontAwesomeIcon :icon="faCheckCircle" />
                <span>{{ valuesItems[0]?.action || 'Stay True' }}</span>
              </div>
            </div>
          </div>

          <!-- Inspiration -->
          <div
            class="group relative bg-white p-10 rounded-3xl border-2 border-gray-100 hover:border-primary transition-all duration-500 hover:shadow-2xl hover:-translate-y-2"
          >
            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2">
              <div
                class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center text-white text-3xl shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all"
              >
                <FontAwesomeIcon :icon="faLightbulb" />
              </div>
            </div>
            <div class="mt-8 text-center">
              <h3
                class="text-2xl font-bold text-primary mb-4 group-hover:text-secondary transition-colors"
              >
                {{ valuesItems[1]?.title || 'Inspiration' }}
              </h3>
              <p class="text-gray-600 text-lg leading-relaxed mb-4">
                {{
                  valuesItems[1]?.description ||
                  "Spark curiosity and adventure in others. Every story has the power to inspire someone's next journey."
                }}
              </p>
              <div class="flex items-center justify-center space-x-2 text-primary font-semibold">
                <FontAwesomeIcon :icon="faLightbulb" />
                <span>
                  {{ valuesItems[1]?.action || 'Light the Way' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Community -->
          <div
            class="group relative bg-white p-10 rounded-3xl border-2 border-gray-100 hover:border-primary transition-all duration-500 hover:shadow-2xl hover:-translate-y-2"
          >
            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2">
              <div
                class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-600 rounded-2xl flex items-center justify-center text-white text-3xl shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all"
              >
                <FontAwesomeIcon :icon="faUsers" />
              </div>
            </div>
            <div class="mt-8 text-center">
              <h3
                class="text-2xl font-bold text-primary mb-4 group-hover:text-secondary transition-colors"
              >
                {{ valuesItems[2]?.title || 'Community' }}
              </h3>
              <p class="text-gray-600 text-lg leading-relaxed mb-4">
                {{
                  valuesItems[2]?.description ||
                  'Connect with fellow explorers worldwide. Together, we create a global network of adventure seekers.'
                }}
              </p>
              <div class="flex items-center justify-center space-x-2 text-primary font-semibold">
                <FontAwesomeIcon :icon="faUsers" />
                <span>
                  {{ valuesItems[2]?.action || 'Grow Together' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 bg-accent">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="relative">
          <div
            class="absolute inset-0 bg-gradient-to-br from-primary via-secondary to-primary rounded-3xl blur-2xl opacity-20"
          ></div>
          <div
            class="relative bg-gradient-to-br from-primary via-secondary to-primary text-accent rounded-3xl p-12 md:p-16"
          >
            <div class="inline-block mb-6">
              <FontAwesomeIcon :icon="faMountain" class="text-6xl" />
            </div>
            <h3 class="text-4xl md:text-5xl font-black mb-6 leading-tight">
              {{ pageContent?.sections?.cta?.title || 'Join Our Community' }}
            </h3>
            <p class="text-xl opacity-95 mb-10 max-w-2xl mx-auto leading-relaxed">
              {{
                pageContent?.sections?.cta?.subtitle ||
                'Start documenting your adventures today and become part of a global community of explorers.'
              }}
            </p>
            <button
              class="group bg-accent text-primary px-12 py-5 rounded-2xl font-black text-xl hover:scale-105 transition-all duration-300 shadow-2xl inline-flex items-center space-x-4"
            >
              <FontAwesomeIcon
                :icon="faUsers"
                class="text-2xl group-hover:scale-110 transition-transform"
              />
              <span>{{ pageContent?.sections?.cta?.buttonText || 'Get Started Free' }}</span>
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
  .theme-page {
    color: var(--text-primary);
    background-color: var(--bg-primary);
  }

  /* Apply CSS variables to all color classes */
  .text-primary {
    color: var(--color-primary) !important;
  }
  .bg-primary {
    background-color: var(--color-primary) !important;
  }
  .border-primary {
    border-color: var(--color-primary) !important;
  }

  .text-secondary {
    color: var(--color-secondary) !important;
  }
  .bg-secondary {
    background-color: var(--color-secondary) !important;
  }
  .border-secondary {
    border-color: var(--color-secondary) !important;
  }

  .text-accent {
    color: var(--color-accent) !important;
  }
  .bg-accent {
    background-color: var(--color-accent) !important;
  }
  .border-accent {
    border-color: var(--color-accent) !important;
  }

  /* Animations - Matching Home Page */
  @keyframes fade-in-down {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fade-in-up {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes float {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-20px);
    }
  }

  @keyframes float-delayed {
    0%,
    100% {
      transform: translateY(0px) rotate(0deg);
    }
    50% {
      transform: translateY(-30px) rotate(5deg);
    }
  }

  @keyframes float-slow {
    0%,
    100% {
      transform: translateY(0px);
    }
    50% {
      transform: translateY(-15px);
    }
  }

  .animate-fade-in-down {
    animation: fade-in-down 0.6s ease-out;
  }

  .animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out;
  }

  .animate-fade-in-up.delay-200 {
    animation-delay: 0.2s;
    opacity: 0;
    animation-fill-mode: forwards;
  }

  .animate-fade-in-up.delay-400 {
    animation-delay: 0.4s;
    opacity: 0;
    animation-fill-mode: forwards;
  }

  .animate-fade-in-up.delay-600 {
    animation-delay: 0.6s;
    opacity: 0;
    animation-fill-mode: forwards;
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
  }

  .animate-float-delayed {
    animation: float-delayed 7s ease-in-out infinite;
  }

  .animate-float-slow {
    animation: float-slow 8s ease-in-out infinite;
  }

  .animate-pulse {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }

  .delay-1000 {
    animation-delay: 1s;
  }

  .delay-2000 {
    animation-delay: 2s;
  }

  /* Shadow Variations */
  .shadow-3xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  }
</style>
