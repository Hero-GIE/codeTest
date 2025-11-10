<script setup>
  import { ref, reactive, computed } from 'vue';
  import { Head, Link, router, useForm } from '@inertiajs/vue3';
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  import {
    faEdit,
    faCalendar,
    faEye,
    faArrowLeft,
    faLocationDot,
    faUpload,
    faImage,
    faHeading,
    faTextWidth,
    faList,
    faPalette,
    faExternalLinkAlt,
    faFileAlt,
    faDesktop,
    faCheckCircle,
    faBars,
    faTimes,
    faSpinner,
    faCompass,
    faMountainSun,
    faPlus,
    faTrash,
    faChartBar,
    faQuestionCircle,
    faMapMarkedAlt,
    faGlobe,
    faLightbulb,
    faImages,
  } from '@fortawesome/free-solid-svg-icons';
  import DeleteImageConfirmationDialog from '../Auth/DeleteImageConfirmationDialog.vue';

  const props = defineProps({
    user: Object,
    page: String,
    pageContent: Object,
    websiteSettings: Object,
    colorPalettes: Object,
  });

  const editingSection = ref(null);
  const editedContent = reactive({ ...props.pageContent });
  const imageUploading = ref(false);
  const saving = ref(false);
  const mobileMenuOpen = ref(false);

  const saveContent = async () => {
    saving.value = true;
    try {
      await router.post(`/website/pages/${props.page}`, editedContent);
      setTimeout(() => (saving.value = false), 1000);
    } catch (error) {
      console.error('Error saving content:', error);
      saving.value = false;
    }
  };

  // Add these methods to your existing methods object
  const addHeroStat = () => {
    if (!editedContent.sections.hero.stats) {
      editedContent.sections.hero.stats = [];
    }
    editedContent.sections.hero.stats.push({
      number: '0+',
      label: 'New Stat',
      description: 'By our community of explorers',
    });
    saveContent();
  };

  const removeHeroStat = (index) => {
    if (editedContent.sections.hero.stats) {
      editedContent.sections.hero.stats.splice(index, 1);
      saveContent();
    }
  };

  // Add these methods to your existing methods object
  const addGalleryCategory = () => {
    if (!editedContent.sections.hero.categories) {
      editedContent.sections.hero.categories = [];
    }
    editedContent.sections.hero.categories.push('New Category');
    saveContent();
  };

  const removeGalleryCategory = (index) => {
    if (editedContent.sections.hero.categories) {
      editedContent.sections.hero.categories.splice(index, 1);
      saveContent();
    }
  };

  // Initialize sections if they don't exist
  if (!editedContent.sections) {
    editedContent.sections = {};
  }

  // Ensure all required sections exist for home page
  if (props.page === 'home') {
    if (!editedContent.sections.hero) {
      editedContent.sections.hero = {
        title: 'Explore The',
        subtitle:
          'Document your journeys, share your stories, and inspire others with your adventures.',
      };
    }
    if (!editedContent.sections.features) {
      editedContent.sections.features = {
        title: 'Amazing Features',
        items: [
          {
            title: 'Interactive Maps',
            description: 'Track and visualize your adventures with beautiful interactive maps.',
          },
          {
            title: 'Photo Gallery',
            description: 'Create stunning visual stories with our easy-to-use photo gallery.',
          },
          {
            title: 'Community Stories',
            description: 'Connect with fellow adventurers and share experiences.',
          },
        ],
      };
    }
    if (!editedContent.sections.recent) {
      editedContent.sections.recent = {
        title: 'Recent Adventures',
        posts: [],
      };
    }
    if (!editedContent.sections.mission) {
      editedContent.sections.mission = {
        title: 'Our Mission',
        content: 'We believe every adventure has a story worth telling.',
        stats: [
          { number: '0+', label: 'Adventures Logged' },
          { number: '0+', label: 'Countries Covered' },
          { number: '0+', label: 'Photos Shared' },
          { number: '1', label: 'Happy Explorer' },
        ],
      };
    }
  }

  // Ensure all required sections exist for contact page
  if (props.page === 'contact') {
    if (!editedContent.sections) {
      editedContent.sections = {};
    }
    if (!editedContent.sections.hero) {
      editedContent.sections.hero = {
        title: 'Get In Touch',
        subtitle: "We'd love to hear about your adventures and help you share them with the world",
      };
    }
    if (!editedContent.sections.info) {
      editedContent.sections.info = {
        title: "Let's Start a Conversation",
        description:
          "Whether you have questions about documenting your adventures, need technical support, or just want to share an amazing story, we're here to help.",
      };
    }
    if (!editedContent.sections.social) {
      editedContent.sections.social = {
        title: 'Follow Our Adventures',
      };
    }
    if (!editedContent.sections.faq) {
      editedContent.sections.faq = {
        title: 'Frequently Asked Questions',
        description: 'Quick answers to common questions',
        items: [
          {
            q: 'How do I start documenting my adventures?',
            a: 'Simply create an account and start adding your first adventure story with photos and descriptions.',
            icon: 'faMapMarkedAlt',
          },
          {
            q: 'Is there a mobile app?',
            a: 'Yes! Our mobile app lets you document adventures on the go with real-time photo uploads.',
            icon: 'faMobileAlt',
          },
          {
            q: 'Can I collaborate with friends?',
            a: 'Absolutely! You can create shared adventure logs with multiple contributors.',
            icon: 'faUsers',
          },
          {
            q: 'Is my data secure?',
            a: 'We use enterprise-grade security to protect your stories and personal information.',
            icon: 'faShieldAlt',
          },
        ],
      };
    }
    if (!editedContent.email) {
      editedContent.email = 'hello@example.com';
    }
    if (!editedContent.social) {
      editedContent.social = {
        instagram: '@myadventures',
        twitter: '@adventurelog',
        facebook: 'myadventurepage',
      };
    }
  }

  // Ensure all required sections exist for gallery page
  if (props.page === 'gallery') {
    if (!editedContent.sections) {
      editedContent.sections = {};
    }
    if (!editedContent.sections.hero) {
      editedContent.sections.hero = {
        title: 'Adventure Gallery',
        subtitle: 'Visual stories from incredible journeys around the world',
        badge: 'Welcome to Your Adventure Log',
        backgroundImage:
          'https://plus.unsplash.com/premium_photo-1709371824843-2b72258fbd71?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1267',
        categories: [
          'Mountain Expeditions',
          'Forest Trails',
          'Coastal Adventures',
          'Desert Journeys',
          'Urban Exploration',
        ],
        cta1Title: 'Browse Gallery',
        cta1Subtitle: 'Explore stunning visuals',
        cta2Title: 'Share Your Story',
        cta2Subtitle: 'Upload your adventures',
      };
      // Save the initial content
      saveContent();
    }
  }

  // Add these reactive variables
  const showAdventureModal = ref(false);
  const savingAdventure = ref(false);
  const editingAdventureIndex = ref(null);

  const newAdventure = ref({
    title: '',
    excerpt: '',
    image: '',
    date: '',
    location: '',
  });

  // Adventure methods
  const openAdventureModal = () => {
    showAdventureModal.value = true;
    editingAdventureIndex.value = null;
    resetAdventureForm();
  };

  const closeAdventureModal = () => {
    showAdventureModal.value = false;
    editingAdventureIndex.value = null;
    resetAdventureForm();
  };

  const resetAdventureForm = () => {
    newAdventure.value = {
      title: '',
      excerpt: '',
      image: '',
      date: '',
      location: '',
    };
  };

  const editAdventure = (adventure, index) => {
    newAdventure.value = { ...adventure };
    editingAdventureIndex.value = index;
    showAdventureModal.value = true;
  };

  const saveAdventure = async () => {
    if (
      !newAdventure.value.title ||
      !newAdventure.value.excerpt ||
      !newAdventure.value.image ||
      !newAdventure.value.date
    ) {
      alert('Please fill in all required fields');
      return;
    }

    savingAdventure.value = true;

    try {
      // ✅ FIX: Save to the correct Firebase path
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

      const response = await fetch('/adventures/create', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
        },
        body: JSON.stringify(newAdventure.value),
      });

      if (!response.ok) {
        const errorText = await response.text();
        console.error('Server error:', errorText);
        throw new Error('Failed to create adventure in database');
      }

      const result = await response.json();
      console.log('✅ Adventure created:', result);

      if (result.success) {
        // ✅ FIX: Declare adventureWithId BEFORE using it
        const adventureWithId = {
          ...newAdventure.value,
          id: result.id || result.adventure?.id || Date.now().toString(),
          createdAt: new Date().toISOString(),
        };

        // Add debug logging - NOW adventureWithId is defined
        console.log('✅ Adventure saved to Firebase path:');
        console.log('/websites/iIYNp0xgmIRoOs9RwG7b9F6W4vv1/pages/home/sections/recent/posts');
        console.log('New adventure data:', adventureWithId);

        // Ensure posts array exists
        if (!editedContent.sections.recent.posts) {
          editedContent.sections.recent.posts = [];
        }

        // Add the new adventure with its ID to local state
        editedContent.sections.recent.posts.unshift(adventureWithId);

        console.log(
          '✅ Adventure added to local state, total:',
          editedContent.sections.recent.posts.length
        );

        // Close modal and reset form
        closeAdventureModal();

        // Show success message
        alert('Adventure created successfully!');
      } else {
        throw new Error(result.error || 'Failed to create adventure');
      }
    } catch (error) {
      console.error('❌ Error saving adventure:', error);
      alert('Failed to save adventure: ' + error.message);
    } finally {
      savingAdventure.value = false;
    }
  };

  const deleteAdventure = async (index) => {
    if (confirm('Are you sure you want to delete this adventure?')) {
      editedContent.sections.recent.posts.splice(index, 1);
      await saveContent();
    }
  };

  // Update the createNewAdventure method to use the modal
  const createNewAdventure = () => {
    showAdventureModal.value = true;
  };

  // Add to your editor component script
  const newImage = ref({
    caption: '',
    location: '',
  });
  const uploading = ref(false);
  const fileInput = ref(null);

  const handleFileUpload = (event) => {
    const file = event.target.files?.[0];

    if (!file) {
      console.warn('No file selected.');
      return;
    }

    // ✅ Validation: allowed file types
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
      alert('Invalid file type. Please upload a JPG, PNG, or WEBP image.');
      console.warn('Invalid file type:', file.type);
      fileInput.value.value = ''; // reset
      return;
    }

    // ✅ Validation: max file size (5MB)
    const maxSizeMB = 5;
    if (file.size > maxSizeMB * 1024 * 1024) {
      alert(`File too large. Please upload an image smaller than ${maxSizeMB}MB.`);
      console.warn(`File size exceeded: ${file.size / 1024 / 1024}MB`);
      fileInput.value.value = ''; // reset
      return;
    }

    console.log('File selected:', file.name, 'Type:', file.type, 'Size:', file.size);
  };

  const uploadImage = async () => {
    const file = fileInput.value?.files?.[0];

    if (!file) {
      alert('Please select a file to upload');
      console.warn('Upload aborted: no file selected');
      return;
    }

    uploading.value = true;
    console.log('Uploading image:', file.name);

    try {
      const formData = new FormData();
      formData.append('image', file);
      formData.append('caption', newImage.value.caption || '');
      formData.append('location', newImage.value.location || '');

      console.log('Form data prepared:', {
        caption: newImage.value.caption,
        location: newImage.value.location,
      });

      // Get CSRF token from meta tag
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

      if (!csrfToken) {
        console.error('CSRF token not found');
        throw new Error('CSRF token is missing. Please refresh the page.');
      }

      console.log('CSRF token found:', csrfToken ? 'Yes' : 'No');

      const response = await fetch('/gallery/upload', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
        },
        body: formData,
      });

      if (!response.ok) {
        const errorText = await response.text();
        console.error('Server responded with status:', response.status, errorText);
        throw new Error(`Upload failed (status ${response.status})`);
      }

      const result = await response.json();
      console.log('Upload response:', result);

      if (result.success && result.image) {
        if (!editedContent.images) {
          editedContent.images = [];
        }

        editedContent.images.push({
          ...result.image,
          id: result.firebase_key || result.image.id || Date.now().toString(),
        });

        console.log('Image added to gallery:', result.image);

        // Reset form
        newImage.value = { caption: '', location: '' };
        fileInput.value.value = '';

        await saveContent();
        console.log('Gallery content saved successfully.');
      } else {
        const errorMsg = result.error || 'Unknown error during upload';
        console.error('Upload failed:', errorMsg);
        alert('Upload failed: ' + errorMsg);
      }
    } catch (error) {
      console.error('Upload error:', error);
      alert('Upload failed: ' + error.message);
    } finally {
      uploading.value = false;
      console.log('Upload process finished.');
    }
  };

  const updateImage = async (image) => {
    if (!image?.id) {
      console.warn('Update aborted: missing image ID.');
      return;
    }

    console.log('Updating image:', image.id);

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

      if (!csrfToken) {
        throw new Error('CSRF token is missing. Please refresh the page.');
      }

      const response = await fetch(`/gallery/image/${image.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
        },
        body: JSON.stringify({
          caption: image.caption || '',
          location: image.location || '',
        }),
      });

      if (!response.ok) {
        const errorText = await response.text();
        console.error('Update failed, status:', response.status, errorText);
        throw new Error(`Failed to update image (status ${response.status})`);
      }

      const result = await response.json();
      console.log('Update response:', result);

      if (!result.success) {
        alert('Update failed: ' + (result.error || 'Unknown error'));
      } else {
        console.log('Image updated successfully:', image.id);
      }
    } catch (error) {
      console.error('Update error:', error);
      alert('Update failed: ' + error.message);
    }
  };

  // Add these new reactive variables
  const showDeleteDialog = ref(false);
  const imageToDelete = ref(null);
  const deleting = ref(false);

  // Replace the deleteImage function with this:
  const openDeleteDialog = (image) => {
    imageToDelete.value = image;
    showDeleteDialog.value = true;
  };

  const deleteImage = async () => {
    if (!imageToDelete.value?.publitio_id || !imageToDelete.value?.id) {
      console.warn('Delete aborted: missing image identifiers.', imageToDelete.value);
      showDeleteDialog.value = false;
      return;
    }

    deleting.value = true;
    console.log(
      'Deleting image:',
      imageToDelete.value.publitio_id,
      'Firebase key:',
      imageToDelete.value.id
    );

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

      if (!csrfToken) {
        throw new Error('CSRF token is missing. Please refresh the page.');
      }

      const response = await fetch(
        `/gallery/image/${imageToDelete.value.publitio_id}?firebase_key=${imageToDelete.value.id}`,
        {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            Accept: 'application/json',
          },
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        console.error('Delete request failed, status:', response.status, errorText);
        throw new Error(`Failed to delete image (status ${response.status})`);
      }

      const result = await response.json();
      console.log('Delete response:', result);

      if (result.success) {
        // Remove image from local state
        editedContent.images = editedContent.images.filter(
          (img) => img.id !== imageToDelete.value.id
        );
        console.log('Image removed locally:', imageToDelete.value.id);

        // Show success message (optional)
        // You can add a toast notification here if you have one

        await saveContent();
        console.log('Gallery content saved after delete.');
      } else {
        throw new Error(result.error || 'Unknown error during deletion');
      }
    } catch (error) {
      console.error('Delete error:', error);
      // Show error message to user
      alert('Delete failed: ' + error.message);
    } finally {
      deleting.value = false;
      showDeleteDialog.value = false;
      imageToDelete.value = null;
    }
  };

  const cancelDelete = () => {
    showDeleteDialog.value = false;
    imageToDelete.value = null;
    deleting.value = false;
  };

  const startEditing = (sectionPath) => {
    editingSection.value = sectionPath;
  };

  const stopEditing = () => {
    editingSection.value = null;
    saveContent();
  };

  const addFeatureItem = () => {
    if (!editedContent.sections.features.items) {
      editedContent.sections.features.items = [];
    }
    editedContent.sections.features.items.push({
      title: 'New Feature',
      description: 'Feature description',
    });
  };

  const addFAQItem = () => {
    if (!editedContent.sections.faq.items) {
      editedContent.sections.faq.items = [];
    }
    editedContent.sections.faq.items.push({
      q: 'New Question?',
      a: 'Answer to the question...',
    });
  };

  const removeFAQItem = (index) => {
    editedContent.sections.faq.items.splice(index, 1);
  };

  const removeFeatureItem = (index) => {
    editedContent.sections.features.items.splice(index, 1);
  };

  const addMissionStat = () => {
    if (!editedContent.sections.mission.stats) {
      editedContent.sections.mission.stats = [];
    }
    editedContent.sections.mission.stats.push({
      number: '0+',
      label: 'New Stat',
    });
  };

  const removeMissionStat = (index) => {
    editedContent.sections.mission.stats.splice(index, 1);
  };

  const addMissionPoint = () => {
    if (!editedContent.sections.mission.points) {
      editedContent.sections.mission.points = [];
    }
    editedContent.sections.mission.points.push('New point...');
  };

  const removeMissionPoint = (index) => {
    editedContent.sections.mission.points.splice(index, 1);
  };

  const addFeatureCard = () => {
    if (!editedContent.sections.featureCards) {
      editedContent.sections.featureCards = [];
    }
    editedContent.sections.featureCards.push({
      title: 'New Feature',
      description: 'Description...',
      icon: 'faHeart',
    });
  };

  const removeFeatureCard = (index) => {
    editedContent.sections.featureCards.splice(index, 1);
  };

  const initializeContactPage = () => {
    editedContent.sections = {
      hero: {
        title: 'Get In Touch',
        subtitle: "We'd love to hear about your adventures and help you share them with the world",
        badge: "Let's Connect & Collaborate",
        highlightedTitle: 'Start Your Journey',
        backgroundImage:
          'https://images.unsplash.com/photo-1596524430615-b46475ddff6e?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170',
        emailTitle: 'Email Us',
        emailSubtitle: 'We reply within 1 hour',
        phoneTitle: 'Call Us',
        phoneNumber: '+1 (555) 123-4567',
        phoneSubtitle: 'Mon – Fri: 9AM – 6PM',
        locationTitle: 'Visit Us',
        addressLine1: '123 Adventure Lane',
        addressLine2: 'Los Angeles, CA',
        cta1Title: 'Send Message',
        cta1Subtitle: 'Get instant response',
        cta2Title: 'Schedule Call',
        cta2Subtitle: 'Book a meeting',
      },
      info: {
        title: "Let's Start a Conversation",
        description:
          "Whether you have questions about documenting your adventures, need technical support, or just want to share an amazing story, we're here to help.",
      },
      social: {
        title: 'Follow Our Adventures',
      },
      faq: {
        title: 'Frequently Asked Questions',
        description: 'Quick answers to common questions',
        items: [
          {
            q: 'How do I start documenting my adventures?',
            a: 'Simply create an account and start adding your first adventure story with photos and descriptions.',
            icon: 'faMapMarkedAlt',
          },
          {
            q: 'Is there a mobile app?',
            a: 'Yes! Our mobile app lets you document adventures on the go with real-time photo uploads.',
            icon: 'faMobileAlt',
          },
          {
            q: 'Can I collaborate with friends?',
            a: 'Absolutely! You can create shared adventure logs with multiple contributors.',
            icon: 'faUsers',
          },
          {
            q: 'Is my data secure?',
            a: 'We use enterprise-grade security to protect your stories and personal information.',
            icon: 'faShieldAlt',
          },
        ],
      },
    };
    editedContent.email = 'hello@example.com';
    editedContent.social = {
      instagram: '@myadventures',
      twitter: '@adventurelog',
      facebook: 'myadventurepage',
    };
    saveContent();
  };

  const initializeAboutPage = () => {
    editedContent.sections = {
      hero: {
        badge: 'Our Story & Mission',
        title: 'About Us',
        highlightedTitle: 'Adventure Journey',
        subtitle:
          'Discover the story behind Adventure Log and the passionate team dedicated to helping you document and share your journeys with the world.',
        stats: [
          { number: '5K+', label: 'Team Members', description: 'Passionate Adventurers' },
          { number: '50+', label: 'Countries Reached', description: 'Global Community' },
          { number: '3+', label: 'Years of Passion', description: 'Dedicated Service' },
        ],
      },
      mission: {
        title: 'OUR MISSION',
        heading: 'Empowering Adventurers Worldwide',
        points: [
          'Born from a passion for exploration and storytelling, Adventure Log was created to bridge the gap between memorable experiences and lasting documentation.',
          "We understand that every journey, whether it's climbing mountains or exploring local hidden gems, deserves to be remembered and shared in a beautiful, meaningful way.",
          'Our platform combines intuitive design with powerful features to help you create stunning visual narratives of your adventures.',
        ],
        quote:
          '"Every adventure is a story waiting to be told. We\'re here to help you tell yours in the most beautiful way possible."',
        quoteAuthor: '— The Adventure Log Team',
      },
      featureCards: [
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
      ],
      values: {
        badge: 'WHAT DRIVES US',
        title: 'Our Core Values',
        subtitle: 'The principles that guide everything we do',
        items: [
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
        ],
      },
      cta: {
        title: 'Join Our Community',
        subtitle:
          'Start documenting your adventures today and become part of a global community of explorers.',
        buttonText: 'Get Started Free',
      },
    };
    editedContent.stats = {
      team_members: '5K+',
      countries_reached: '50+',
      years_of_passion: '3+',
    };
    saveContent();
  };

  const initializeHomePage = () => {
    editedContent.sections = {
      hero: {
        badge: 'Welcome to Your Adventure Log',
        title: 'Welcome to My Adventure Log',
        subtitle: 'Documenting my journeys and experiences',
        text: 'Start your adventure and share your stories with the world.',
        backgroundImage:
          'https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80',
        cta1Title: 'Explore Adventures',
        cta1Subtitle: 'Start your journey today',
        cta2Title: 'We guide your path',
        cta2Subtitle: 'Get into action',
        stats: [
          { number: '100+', label: 'Adventures', description: 'By our community' },
          { number: '50+', label: 'Countries', description: 'Explored worldwide' },
          { number: '1k+', label: 'Photos', description: 'Memories captured' },
        ],
      },
      features: {
        title: 'What I Do',
        items: [
          {
            title: 'Adventure Logging',
            description: 'Track and share my adventures',
            icon: '🚀',
          },
          {
            title: 'Story Telling',
            description: 'Share experiences and memories',
            icon: '📖',
          },
          {
            title: 'Photo Journal',
            description: 'Visual journey through photos',
            icon: '📷',
          },
        ],
      },
      mission: {
        title: 'Our Mission',
        content:
          'We believe every adventure has a story worth telling. Our mission is to provide the tools and platform for adventurers to document, share, and inspire others with their journeys.',
        stats: [
          { number: '0+', label: 'Adventures Logged' },
          { number: '0+', label: 'Countries Covered' },
          { number: '0+', label: 'Photos Shared' },
          { number: '1', label: 'Happy Explorer' },
        ],
      },
      recent: {
        title: 'Recent Adventures',
        posts: [
          {
            title: 'Mountain Hiking',
            date: '2024-01-15',
            image:
              'https://images.unsplash.com/photo-1454496522488-7a8e488e8606?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1176&q=80',
            excerpt: 'Amazing views from the summit...',
          },
          {
            title: 'Beach Vacation',
            date: '2024-01-10',
            image:
              'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1173&q=80',
            excerpt: 'Relaxing days by the ocean...',
          },
        ],
      },
    };
    editedContent.title = 'My Adventure Log';
    editedContent.published = true;
    saveContent();
  };

  const pageTitle = computed(() => {
    return props.page.charAt(0).toUpperCase() + props.page.slice(1) + ' Page Editor';
  });
</script>

<template>
  <Head :title="pageTitle" />

  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50/30">
    <!-- Mobile Menu Overlay -->
    <div
      v-if="mobileMenuOpen"
      class="fixed inset-0 bg-black bg-opacity-50 z-60 lg:hidden"
      @click="mobileMenuOpen = false"
    ></div>

    <!-- Editor Header -->
    <nav class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-30">
      <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Left Section -->
          <div class="flex items-center space-x-2 sm:space-x-3">
            <button
              @click="mobileMenuOpen = !mobileMenuOpen"
              class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
            >
              <FontAwesomeIcon :icon="mobileMenuOpen ? faTimes : faBars" class="text-lg" />
            </button>

            <div
              class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg flex-shrink-0"
            >
              <FontAwesomeIcon :icon="faEdit" class="text-white text-sm sm:text-lg" />
            </div>
            <div class="min-w-0 flex-1">
              <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">
                Editing:
                <span class="capitalize text-blue-600">{{ page }}</span>
              </h1>
              <p class="text-xs sm:text-sm text-gray-500 truncate">
                Make changes and see instant preview
              </p>
            </div>
          </div>

          <!-- Desktop Actions -->
          <div class="hidden lg:flex items-center space-x-2 lg:space-x-3">
            <Link
              :href="route('dashboard')"
              class="group flex items-center space-x-2 text-gray-500 hover:text-gray-900 px-3 lg:px-4 py-2 rounded-lg transition-all duration-300 hover:bg-gray-100 text-sm lg:text-base"
            >
              <FontAwesomeIcon
                :icon="faArrowLeft"
                class="group-hover:-translate-x-1 transition-transform text-sm"
              />
              <span>Dashboard</span>
            </Link>
            <Link
              :href="route('website.page', { page })"
              class="group flex items-center space-x-2 text-gray-600 hover:text-blue-600 px-3 lg:px-4 py-2 rounded-lg transition-all duration-300 hover:bg-blue-50 text-sm lg:text-base"
              target="_blank"
            >
              <FontAwesomeIcon
                :icon="faExternalLinkAlt"
                class="group-hover:scale-110 transition-transform text-sm"
              />
              <span>Preview</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div
        v-if="mobileMenuOpen"
        class="lg:hidden absolute top-16 left-0 right-0 bg-white border-b border-gray-200 shadow-xl z-50"
      >
        <div class="px-4 py-3 space-y-3">
          <Link
            :href="route('dashboard')"
            class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 py-2 px-3 rounded-lg hover:bg-blue-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            <FontAwesomeIcon :icon="faArrowLeft" class="text-gray-400" />
            <span>Back to Dashboard</span>
          </Link>
          <Link
            :href="route('website.page', { page })"
            class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 py-2 px-3 rounded-lg hover:bg-blue-50 transition-colors"
            target="_blank"
            @click="mobileMenuOpen = false"
          >
            <FontAwesomeIcon :icon="faExternalLinkAlt" class="text-gray-400" />
            <span>Preview Page</span>
          </Link>
        </div>
      </div>
    </nav>

    <div
      class="max-w-7xl mx-auto py-4 sm:py-6 px-3 sm:px-4 lg:px-8 h-[calc(100vh-4rem)] sm:h-[calc(100vh-5rem)]"
    >
      <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 h-full">
        <!-- Editor Panel -->
        <div
          class="group bg-white shadow-lg sm:shadow-xl rounded-xl sm:rounded-2xl border border-gray-100 flex flex-col h-full overflow-hidden"
        >
          <div class="p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center space-x-2 sm:space-x-3">
              <div
                class="w-8 h-8 sm:w-12 sm:h-12 bg-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg flex-shrink-0"
              >
                <FontAwesomeIcon :icon="faFileAlt" class="text-white text-sm sm:text-xl" />
              </div>
              <div class="min-w-0 flex-1">
                <h2 class="text-lg sm:text-2xl font-bold text-gray-900 truncate">Content Editor</h2>
                <p class="text-xs sm:text-sm text-gray-500">Edit your page content in real-time</p>
              </div>
            </div>
          </div>

          <!-- Scrollable Content Area -->
          <div class="flex-1 overflow-y-auto p-3 sm:p-4 lg:p-6 space-y-4 sm:space-y-6">
            <!-- Page Title -->
            <div
              class="p-3 sm:p-4 lg:p-5 border-2 border-gray-200 rounded-lg sm:rounded-xl hover:border-blue-300 transition-all duration-300 bg-white"
            >
              <label
                class="block text-sm font-semibold text-gray-700 mb-2 sm:mb-3 flex items-center space-x-2"
              >
                <FontAwesomeIcon :icon="faHeading" class="text-blue-500 text-sm" />
                <span class="text-xs sm:text-sm">Page Title</span>
              </label>
              <input
                type="text"
                v-model="editedContent.title"
                @blur="saveContent"
                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 text-base sm:text-lg font-medium"
                placeholder="Enter your page title..."
              />
            </div>

            <!-- Home Page Sections -->
            <div v-if="page === 'home'">
              <!-- Initialize Button -->
              <div
                v-if="!editedContent.sections?.hero && !editedContent.sections?.features"
                class="mb-6 p-4 border-2 border-dashed border-purple-300 rounded-xl bg-purple-50/50 text-center"
              >
                <div class="mb-4">
                  <FontAwesomeIcon :icon="faRocket" class="text-purple-500 text-3xl" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Initialize Home Page</h3>
                <p class="text-gray-600 text-sm mb-4">
                  Set up your home page with beautiful default content that you can customize.
                </p>
                <button
                  @click="initializeHomePage"
                  class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition-all flex items-center space-x-2 mx-auto"
                >
                  <FontAwesomeIcon :icon="faPlus" />
                  <span>Initialize Home Page Sections</span>
                </button>
              </div>

              <!-- Hero Section - Enhanced with All Editable Fields -->
              <div
                v-if="editedContent.sections.hero"
                class="p-3 sm:p-4 lg:p-5 border-2 border-purple-200 rounded-lg sm:rounded-xl hover:border-purple-400 transition-all duration-300 bg-purple-50/30"
              >
                <h3
                  class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2"
                >
                  <div
                    class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0"
                  >
                    <FontAwesomeIcon :icon="faPalette" class="text-white text-xs sm:text-sm" />
                  </div>
                  <span>Hero Section</span>
                </h3>

                <div class="space-y-4">
                  <!-- Badge -->
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Badge Text
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.badge"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="Welcome to Your Adventure Log"
                    />
                  </div>

                  <!-- Background Image -->
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Background Image URL
                    </label>
                    <input
                      type="url"
                      v-model="editedContent.sections.hero.backgroundImage"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="https://images.unsplash.com/photo-..."
                    />
                    <p class="text-xs text-gray-500 mt-1">
                      Pro tip: Use high-quality landscape images from
                      <a
                        href="https://unsplash.com"
                        target="_blank"
                        class="text-purple-600 hover:underline"
                      >
                        Unsplash
                      </a>
                    </p>
                  </div>

                  <!-- Hero Title -->
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Hero Title
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.title"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="Welcome to My Adventure Log"
                    />
                  </div>

                  <!-- Hero Subtitle -->
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Hero Subtitle
                    </label>
                    <textarea
                      v-model="editedContent.sections.hero.subtitle"
                      @blur="saveContent"
                      rows="3"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm resize-vertical"
                      placeholder="Documenting my journeys and experiences"
                    />
                  </div>

                  <!-- Hero Text -->
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Hero Text
                    </label>
                    <textarea
                      v-model="editedContent.sections.hero.text"
                      @blur="saveContent"
                      rows="2"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm resize-vertical"
                      placeholder="Start your adventure and share your stories with the world."
                    />
                  </div>

                  <!-- CTA Cards -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- CTA 1 -->
                    <div class="p-3 border-2 border-amber-200 rounded-lg bg-amber-50/30">
                      <h4 class="text-xs font-semibold text-amber-800 mb-2">First CTA Card</h4>
                      <div class="space-y-2">
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.cta1Title"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Explore Adventures"
                        />
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.cta1Subtitle"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Start your journey today"
                        />
                      </div>
                    </div>

                    <!-- CTA 2 -->
                    <div class="p-3 border-2 border-emerald-200 rounded-lg bg-emerald-50/30">
                      <h4 class="text-xs font-semibold text-emerald-800 mb-2">Second CTA Card</h4>
                      <div class="space-y-2">
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.cta2Title"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="We guide your path"
                        />
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.cta2Subtitle"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Get into action"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Hero Stats -->
                  <div>
                    <div class="flex items-center justify-between mb-2">
                      <label class="block text-xs sm:text-sm font-medium text-gray-700">
                        Hero Stats
                      </label>
                      <button
                        @click="addHeroStat"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-2 py-1 rounded text-xs font-semibold transition-all flex items-center space-x-1"
                      >
                        <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                        <span>Add Stat</span>
                      </button>
                    </div>

                    <div class="space-y-2">
                      <div
                        v-for="(stat, index) in editedContent.sections.hero.stats || []"
                        :key="index"
                        class="p-3 border-2 border-gray-200 rounded-lg bg-white relative"
                      >
                        <button
                          @click="removeHeroStat(index)"
                          class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1 rounded-lg hover:bg-red-50 transition-all"
                        >
                          <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                        </button>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pr-8">
                          <div>
                            <label class="block text-xs text-gray-600 mb-1 font-medium">
                              Number
                            </label>
                            <input
                              type="text"
                              v-model="stat.number"
                              @blur="saveContent"
                              class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                              placeholder="e.g., 100+"
                            />
                          </div>
                          <div>
                            <label class="block text-xs text-gray-600 mb-1 font-medium">
                              Label
                            </label>
                            <input
                              type="text"
                              v-model="stat.label"
                              @blur="saveContent"
                              class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                              placeholder="e.g., Adventures"
                            />
                          </div>
                          <div>
                            <label class="block text-xs text-gray-600 mb-1 font-medium">
                              Description
                            </label>
                            <input
                              type="text"
                              v-model="stat.description"
                              @blur="saveContent"
                              class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                              placeholder="e.g., By our community"
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Features Section -->
              <div
                v-if="editedContent.sections.features"
                class="p-3 sm:p-4 lg:p-5 border-2 border-blue-200 rounded-lg sm:rounded-xl hover:border-blue-400 transition-all duration-300 bg-blue-50/30"
              >
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                  <h3
                    class="text-base sm:text-lg font-bold text-gray-900 flex items-center space-x-2"
                  >
                    <div
                      class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0"
                    >
                      <FontAwesomeIcon :icon="faList" class="text-white text-xs sm:text-sm" />
                    </div>
                    <span>Features Section</span>
                  </h3>
                  <button
                    @click="addFeatureItem"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all flex items-center space-x-1"
                  >
                    <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                    <span>Add Feature</span>
                  </button>
                </div>

                <div class="space-y-3">
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Section Title
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.features.title"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                      placeholder="What I Do"
                    />
                  </div>

                  <div class="space-y-3">
                    <div
                      v-for="(item, index) in editedContent.sections.features.items"
                      :key="index"
                      class="p-3 sm:p-4 border-2 border-gray-200 rounded-lg bg-white relative"
                    >
                      <button
                        @click="removeFeatureItem(index)"
                        class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-all"
                      >
                        <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                      </button>

                      <div class="space-y-2 pr-8">
                        <div>
                          <label class="block text-xs text-gray-600 mb-1 font-medium">
                            Feature Title
                          </label>
                          <input
                            type="text"
                            v-model="item.title"
                            @blur="saveContent"
                            class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm"
                            placeholder="Feature title..."
                          />
                        </div>
                        <div>
                          <label class="block text-xs text-gray-600 mb-1 font-medium">
                            Description
                          </label>
                          <textarea
                            v-model="item.description"
                            @blur="saveContent"
                            rows="2"
                            class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm resize-vertical"
                            placeholder="Feature description..."
                          />
                        </div>
                        <div>
                          <label class="block text-xs text-gray-600 mb-1 font-medium">Icon</label>
                          <input
                            type="text"
                            v-model="item.icon"
                            @blur="saveContent"
                            class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm"
                            placeholder="🚀, 📖, 📷, etc."
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Mission Section -->
              <div
                v-if="editedContent.sections.mission"
                class="p-3 sm:p-4 lg:p-5 border-2 border-green-200 rounded-lg sm:rounded-xl hover:border-green-400 transition-all duration-300 bg-green-50/30"
              >
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                  <h3
                    class="text-base sm:text-lg font-bold text-gray-900 flex items-center space-x-2"
                  >
                    <div
                      class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0"
                    >
                      <FontAwesomeIcon :icon="faChartBar" class="text-white text-xs sm:text-sm" />
                    </div>
                    <span>Mission Section</span>
                  </h3>
                  <button
                    @click="addMissionStat"
                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all flex items-center space-x-1"
                  >
                    <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                    <span>Add Stat</span>
                  </button>
                </div>

                <div class="space-y-3">
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Mission Title
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.mission.title"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                      placeholder="Our Mission"
                    />
                  </div>
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Mission Content
                    </label>
                    <textarea
                      v-model="editedContent.sections.mission.content"
                      @blur="saveContent"
                      rows="3"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm resize-vertical"
                      placeholder="We believe every adventure has a story worth telling..."
                    />
                  </div>

                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Mission Stats
                    </label>
                    <div class="space-y-2">
                      <div
                        v-for="(stat, index) in editedContent.sections.mission.stats"
                        :key="index"
                        class="p-3 border-2 border-gray-200 rounded-lg bg-white relative"
                      >
                        <button
                          @click="removeMissionStat(index)"
                          class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-all"
                        >
                          <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                        </button>

                        <div class="grid grid-cols-2 gap-2 pr-8">
                          <div>
                            <label class="block text-xs text-gray-600 mb-1 font-medium">
                              Number
                            </label>
                            <input
                              type="text"
                              v-model="stat.number"
                              @blur="saveContent"
                              class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-xs"
                              placeholder="e.g., 100+"
                            />
                          </div>
                          <div>
                            <label class="block text-xs text-gray-600 mb-1 font-medium">
                              Label
                            </label>
                            <input
                              type="text"
                              v-model="stat.label"
                              @blur="saveContent"
                              class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-xs"
                              placeholder="e.g., Adventures"
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Recent Adventures Section -->
              <div
                class="p-3 sm:p-4 lg:p-5 border-2 border-orange-200 rounded-lg sm:rounded-xl hover:border-orange-400 transition-all duration-300 bg-orange-50/30"
              >
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                  <h3
                    class="text-base sm:text-lg font-bold text-gray-900 flex items-center space-x-2"
                  >
                    <div
                      class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0"
                    >
                      <FontAwesomeIcon :icon="faImage" class="text-white text-xs sm:text-sm" />
                    </div>
                    <span>Recent Adventures</span>
                  </h3>
                  <button
                    @click="showAdventureModal = true"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all flex items-center space-x-1"
                  >
                    <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                    <span>Create Adventure</span>
                  </button>
                </div>

                <div class="space-y-3">
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Section Title
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.recent.title"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                      placeholder="Recent Adventures"
                    />
                  </div>

                  <!-- Adventures List -->
                  <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700">
                      Your Adventures ({{ editedContent.sections.recent.posts?.length || 0 }})
                    </h4>

                    <!-- No Adventures State -->
                    <div
                      v-if="!editedContent.sections.recent.posts?.length"
                      class="text-center py-8 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50/50"
                    >
                      <div
                        class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4"
                      >
                        <FontAwesomeIcon :icon="faCompass" class="text-orange-500 text-2xl" />
                      </div>
                      <h5 class="text-lg font-semibold text-gray-700 mb-2">No Adventures Yet</h5>
                      <p class="text-gray-500 text-sm max-w-md mx-auto mb-4">
                        Create your first adventure to showcase on your homepage
                      </p>
                      <button
                        @click="showAdventureModal = true"
                        class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-all flex items-center space-x-2 mx-auto"
                      >
                        <FontAwesomeIcon :icon="faPlus" />
                        <span>Create First Adventure</span>
                      </button>
                    </div>

                    <!-- Adventures Grid -->
                    <div v-else class="grid grid-cols-1 gap-4 max-h-96 overflow-y-auto p-2">
                      <div
                        v-for="(adventure, index) in editedContent.sections.recent.posts"
                        :key="adventure.id || index"
                        class="bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-orange-300 transition-all duration-300 relative group"
                      >
                        <div class="flex items-start space-x-4">
                          <!-- Adventure Image -->
                          <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden">
                            <img
                              :src="adventure.image"
                              :alt="adventure.title"
                              class="w-full h-full object-cover"
                            />
                          </div>

                          <!-- Adventure Details -->
                          <div class="flex-1 min-w-0">
                            <h5 class="font-semibold text-gray-900 text-sm mb-1 truncate">
                              {{ adventure.title }}
                            </h5>
                            <p class="text-gray-600 text-xs mb-2 line-clamp-2">
                              {{ adventure.excerpt }}
                            </p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                              <span>{{ adventure.date }}</span>
                              <span>{{ adventure.location || 'No location' }}</span>
                            </div>
                          </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                          class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex space-x-1"
                        >
                          <button
                            @click="editAdventure(adventure, index)"
                            class="bg-blue-500 text-white p-1.5 rounded-lg hover:bg-blue-600 transition-colors"
                            title="Edit Adventure"
                          >
                            <FontAwesomeIcon :icon="faEdit" class="text-xs" />
                          </button>
                          <button
                            @click="deleteAdventure(index)"
                            class="bg-red-500 text-white p-1.5 rounded-lg hover:bg-red-600 transition-colors"
                            title="Delete Adventure"
                          >
                            <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Adventure Creation/Edit Modal -->
            <div
              v-if="showAdventureModal"
              class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm transition-all duration-300"
            >
              <div
                class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 transform transition-all duration-300 scale-95 hover:scale-100"
              >
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-gray-900 to-black text-white p-6 rounded-t-2xl">
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="text-xl font-bold">
                        {{
                          editingAdventureIndex !== null ? 'Edit Adventure' : 'Create New Adventure'
                        }}
                      </h3>
                      <p class="text-gray-300 text-sm mt-1">
                        {{
                          editingAdventureIndex !== null
                            ? 'Update your adventure details'
                            : 'Share your next great adventure'
                        }}
                      </p>
                    </div>
                    <button
                      @click="closeAdventureModal"
                      class="text-gray-300 hover:text-white transition-colors duration-200 bg-black bg-opacity-30 p-2 rounded-full"
                    >
                      <FontAwesomeIcon :icon="faTimes" class="text-lg" />
                    </button>
                  </div>
                </div>

                <!-- Adventure Form -->
                <div class="p-6 space-y-6">
                  <!-- Adventure Title -->
                  <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                      <span>Adventure Title</span>
                      <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input
                      v-model="newAdventure.title"
                      type="text"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all duration-300 bg-gray-50"
                      placeholder="e.g., Mountain Hiking in the Alps"
                    />
                  </div>

                  <!-- Adventure Excerpt -->
                  <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                      <span>Short Description</span>
                      <span class="text-red-500 ml-1">*</span>
                    </label>
                    <textarea
                      v-model="newAdventure.excerpt"
                      required
                      rows="3"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all duration-300 resize-vertical bg-gray-50"
                      placeholder="Brief description of your adventure..."
                    />
                  </div>

                  <!-- Adventure Image -->
                  <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                      <span>Adventure Image URL</span>
                      <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input
                      v-model="newAdventure.image"
                      type="url"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all duration-300 bg-gray-50"
                      placeholder="https://images.unsplash.com/photo-1469474968028-56623f02e42e"
                    />
                    <p class="text-xs text-gray-600 mt-2">
                      Pro tip: Use high-quality images from
                      <a
                        href="https://unsplash.com"
                        target="_blank"
                        class="text-black font-semibold hover:underline"
                      >
                        Unsplash
                      </a>
                    </p>
                  </div>

                  <!-- Date and Location -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center">
                        <span>Adventure Date</span>
                        <span class="text-red-500 ml-1">*</span>
                      </label>
                      <input
                        v-model="newAdventure.date"
                        type="date"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all duration-300 bg-gray-50"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-bold text-gray-900 mb-2">Location</label>
                      <input
                        v-model="newAdventure.location"
                        type="text"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all duration-300 bg-gray-50"
                        placeholder="e.g., Swiss Alps, Switzerland"
                      />
                    </div>
                  </div>

                  <!-- Preview -->
                  <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 bg-gray-50">
                    <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                      <FontAwesomeIcon :icon="faEye" class="mr-2 text-gray-600" />
                      Preview
                    </h4>
                    <div
                      class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md"
                    >
                      <div
                        v-if="newAdventure.image"
                        class="h-32 bg-gradient-to-br from-gray-800 to-black overflow-hidden"
                      >
                        <img
                          :src="newAdventure.image"
                          :alt="newAdventure.title"
                          class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                        />
                      </div>
                      <div
                        v-else
                        class="h-32 bg-gradient-to-br from-gray-800 to-black flex items-center justify-center"
                      >
                        <FontAwesomeIcon :icon="faMountainSun" class="text-white text-2xl" />
                      </div>
                      <div class="p-3">
                        <h5 class="font-bold text-gray-900 text-sm mb-1 truncate">
                          {{ newAdventure.title || 'Adventure Title' }}
                        </h5>
                        <p class="text-gray-700 text-xs line-clamp-2 mb-2">
                          {{ newAdventure.excerpt || 'Adventure description...' }}
                        </p>
                        <div
                          class="flex justify-between items-center text-xs text-gray-600 mt-2 pt-2 border-t border-gray-100"
                        >
                          <span class="flex items-center">
                            <FontAwesomeIcon :icon="faCalendar" class="mr-1 text-gray-500" />
                            {{ newAdventure.date || 'Date' }}
                          </span>
                          <span class="flex items-center">
                            <FontAwesomeIcon :icon="faLocationDot" class="mr-1 text-gray-500" />
                            {{ newAdventure.location || 'Location' }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Modal Footer -->
                <div class="border-t border-gray-200 p-6 bg-gray-50 rounded-b-2xl">
                  <div class="flex flex-col sm:flex-row gap-3">
                    <button
                      @click="saveAdventure"
                      :disabled="
                        !newAdventure.title ||
                        !newAdventure.excerpt ||
                        !newAdventure.image ||
                        !newAdventure.date
                      "
                      class="flex-1 bg-black text-white px-5 py-3 rounded-lg font-bold hover:bg-gray-800 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2 shadow-md hover:shadow-lg"
                    >
                      <FontAwesomeIcon
                        v-if="savingAdventure"
                        :icon="faSpinner"
                        class="animate-spin"
                      />
                      <span class="text-sm">
                        {{
                          editingAdventureIndex !== null ? 'Update Adventure' : 'Create Adventure'
                        }}
                      </span>
                    </button>
                    <button
                      @click="closeAdventureModal"
                      class="flex-1 bg-white text-gray-800 border border-gray-300 px-5 py-3 rounded-lg font-bold hover:bg-gray-100 transition-all duration-300 shadow-sm hover:shadow-md"
                    >
                      <span class="text-sm">Cancel</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- About Page Sections -->
            <div v-if="page === 'about'" class="space-y-6">
              <!-- Initialize Button -->
              <div
                v-if="!editedContent.sections?.hero && !editedContent.sections?.mission"
                class="mb-4"
              >
                <button
                  @click="initializeAboutPage"
                  class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-semibold transition-all flex items-center justify-center space-x-2"
                >
                  <FontAwesomeIcon :icon="faPlus" />
                  <span>Initialize About Page Sections</span>
                </button>
              </div>

              <!-- Hero Section -->
              <div
                v-if="editedContent.sections?.hero"
                class="p-4 border-2 border-purple-200 rounded-xl bg-purple-50/30"
              >
                <h3 class="text-lg font-bold text-gray-900 mb-4">Hero Section</h3>
                <div class="space-y-4">
                  <!-- Badge -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Badge Text</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.badge"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="Our Story & Mission"
                    />
                  </div>

                  <!-- Title -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.title"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="About Our"
                    />
                  </div>

                  <!-- Highlighted Title -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Highlighted Title
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.highlightedTitle"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="Adventure Journey"
                    />
                  </div>

                  <!-- Subtitle -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                    <textarea
                      v-model="editedContent.sections.hero.subtitle"
                      @blur="saveContent"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="Discover the story behind..."
                    />
                  </div>

                  <!-- Hero Stats -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hero Stats</label>
                    <div class="space-y-3">
                      <div
                        v-for="(stat, index) in editedContent.sections.hero.stats || []"
                        :key="index"
                        class="grid grid-cols-3 gap-3 p-3 border border-gray-200 rounded-lg bg-white"
                      >
                        <div>
                          <label class="block text-xs text-gray-600 mb-1">Number</label>
                          <input
                            type="text"
                            v-model="stat.number"
                            @blur="saveContent"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                            placeholder="5K+"
                          />
                        </div>
                        <div>
                          <label class="block text-xs text-gray-600 mb-1">Label</label>
                          <input
                            type="text"
                            v-model="stat.label"
                            @blur="saveContent"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                            placeholder="Team Members"
                          />
                        </div>
                        <div>
                          <label class="block text-xs text-gray-600 mb-1">Description</label>
                          <input
                            type="text"
                            v-model="stat.description"
                            @blur="saveContent"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                            placeholder="Passionate Adventurers"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Mission Section -->
              <div
                v-if="editedContent.sections?.mission"
                class="p-4 border-2 border-green-200 rounded-xl bg-green-50/30"
              >
                <h3 class="text-lg font-bold text-gray-900 mb-4">Mission Section</h3>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Mission Title
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.mission.title"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="OUR MISSION"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Mission Heading
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.mission.heading"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="Empowering Adventurers Worldwide"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Mission Points
                    </label>
                    <div class="space-y-2">
                      <textarea
                        v-for="(point, index) in editedContent.sections.mission.points || []"
                        :key="index"
                        v-model="editedContent.sections.mission.points[index]"
                        @blur="saveContent"
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                        :placeholder="`Mission point ${index + 1}`"
                      />
                    </div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quote</label>
                    <textarea
                      v-model="editedContent.sections.mission.quote"
                      @blur="saveContent"
                      rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="Inspirational quote..."
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quote Author</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.mission.quoteAuthor"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="— The Adventure Log Team"
                    />
                  </div>
                </div>
              </div>

              <!-- Stats Section -->
              <div
                v-if="editedContent.stats"
                class="p-4 border-2 border-blue-200 rounded-xl bg-blue-50/30"
              >
                <h3 class="text-lg font-bold text-gray-900 mb-4">Stats Section</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Team Members</label>
                    <input
                      type="text"
                      v-model="editedContent.stats.team_members"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="5K+"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Countries Reached
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.stats.countries_reached"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="50+"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Years of Passion
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.stats.years_of_passion"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="3+"
                    />
                  </div>
                </div>
              </div>

              <!-- Core Values Section -->
              <div
                v-if="editedContent.sections?.values"
                class="p-4 border-2 border-orange-200 rounded-xl bg-orange-50/30"
              >
                <h3 class="text-lg font-bold text-gray-900 mb-4">Core Values Section</h3>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Values Badge</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.values.badge"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="WHAT DRIVES US"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Values Title</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.values.title"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="Our Core Values"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Values Subtitle
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.values.subtitle"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="The principles that guide everything we do"
                    />
                  </div>
                </div>
              </div>

              <!-- CTA Section -->
              <div
                v-if="editedContent.sections?.cta"
                class="p-4 border-2 border-purple-200 rounded-xl bg-purple-50/30"
              >
                <h3 class="text-lg font-bold text-gray-900 mb-4">Call to Action Section</h3>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CTA Title</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.cta.title"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="Join Our Community"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CTA Subtitle</label>
                    <textarea
                      v-model="editedContent.sections.cta.subtitle"
                      @blur="saveContent"
                      rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="Start documenting your adventures today..."
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.cta.buttonText"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                      placeholder="Get Started Free"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Contact Page Sections -->
            <div v-if="page === 'contact'">
              <!-- Initialize Button -->
              <div v-if="!editedContent.sections.hero && !editedContent.sections.info" class="mb-4">
                <button
                  @click="initializeContactPage"
                  class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-semibold transition-all flex items-center justify-center space-x-2"
                >
                  <FontAwesomeIcon :icon="faPlus" />
                  <span>Initialize Contact Page Sections</span>
                </button>
              </div>

              <!-- Enhanced Hero Section -->
              <div
                v-if="editedContent.sections.hero"
                class="p-3 sm:p-4 lg:p-5 border-2 border-purple-200 rounded-lg sm:rounded-xl hover:border-purple-400 transition-all duration-300 bg-purple-50/30"
              >
                <h3
                  class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2"
                >
                  <div
                    class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0"
                  >
                    <FontAwesomeIcon :icon="faPalette" class="text-white text-xs sm:text-sm" />
                  </div>
                  <span>Contact Hero Section</span>
                </h3>

                <div class="space-y-4">
                  <!-- Background Image -->
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Background Image URL
                    </label>
                    <input
                      type="url"
                      v-model="editedContent.sections.hero.backgroundImage"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="https://images.unsplash.com/photo-..."
                    />
                    <p class="text-xs text-gray-500 mt-1">
                      Use high-quality contact/communication themed images from Unsplash
                    </p>
                  </div>

                  <!-- Badge -->
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Badge Text
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.badge"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="Let's Connect & Collaborate"
                    />
                  </div>

                  <!-- Main Title -->
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Main Title
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.title"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="Get In Touch"
                    />
                  </div>

                  <!-- Highlighted Title -->
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Highlighted Title (Gradient Text)
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.highlightedTitle"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="Start Your Journey"
                    />
                  </div>

                  <!-- Subtitle -->
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Subtitle
                    </label>
                    <textarea
                      v-model="editedContent.sections.hero.subtitle"
                      @blur="saveContent"
                      rows="3"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm resize-vertical"
                      placeholder="We'd love to hear about your adventures..."
                    />
                  </div>

                  <!-- Contact Cards -->
                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Email Card -->
                    <div class="p-3 border-2 border-amber-200 rounded-lg bg-amber-50/30">
                      <h4 class="text-xs font-semibold text-amber-800 mb-2">Email Card</h4>
                      <div class="space-y-2">
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.emailTitle"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Email Us"
                        />
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.emailSubtitle"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="We reply within 1 hour"
                        />
                      </div>
                    </div>

                    <!-- Phone Card -->
                    <div class="p-3 border-2 border-emerald-200 rounded-lg bg-emerald-50/30">
                      <h4 class="text-xs font-semibold text-emerald-800 mb-2">Phone Card</h4>
                      <div class="space-y-2">
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.phoneTitle"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Call Us"
                        />
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.phoneNumber"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="+1 (555) 123-4567"
                        />
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.phoneSubtitle"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Mon – Fri: 9AM – 6PM"
                        />
                      </div>
                    </div>

                    <!-- Location Card -->
                    <div class="p-3 border-2 border-cyan-200 rounded-lg bg-cyan-50/30">
                      <h4 class="text-xs font-semibold text-cyan-800 mb-2">Location Card</h4>
                      <div class="space-y-2">
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.locationTitle"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Visit Us"
                        />
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.addressLine1"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="123 Adventure Lane"
                        />
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.addressLine2"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Los Angeles, CA"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- CTA Cards -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- CTA 1 -->
                    <div class="p-3 border-2 border-amber-200 rounded-lg bg-amber-50/30">
                      <h4 class="text-xs font-semibold text-amber-800 mb-2">First CTA Button</h4>
                      <div class="space-y-2">
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.cta1Title"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Send Message"
                        />
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.cta1Subtitle"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Get instant response"
                        />
                      </div>
                    </div>

                    <!-- CTA 2 -->
                    <div class="p-3 border-2 border-cyan-200 rounded-lg bg-cyan-50/30">
                      <h4 class="text-xs font-semibold text-cyan-800 mb-2">Second CTA Button</h4>
                      <div class="space-y-2">
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.cta2Title"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Schedule Call"
                        />
                        <input
                          type="text"
                          v-model="editedContent.sections.hero.cta2Subtitle"
                          @blur="saveContent"
                          class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                          placeholder="Book a meeting"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Contact Information Section -->
              <div
                v-if="editedContent.sections.info"
                class="p-3 sm:p-4 lg:p-5 border-2 border-blue-200 rounded-lg sm:rounded-xl hover:border-blue-400 transition-all duration-300 bg-blue-50/30"
              >
                <h3
                  class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2"
                >
                  <div
                    class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0"
                  >
                    <FontAwesomeIcon :icon="faMapMarkedAlt" class="text-white text-xs sm:text-sm" />
                  </div>
                  <span>Contact Information</span>
                </h3>

                <div class="space-y-3">
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Section Title
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.info.title"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                      placeholder="Enter section title..."
                    />
                  </div>
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Description
                    </label>
                    <textarea
                      v-model="editedContent.sections.info.description"
                      @blur="saveContent"
                      rows="3"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-vertical"
                      placeholder="Enter description..."
                    />
                  </div>
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Email Address
                    </label>
                    <input
                      type="email"
                      v-model="editedContent.email"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                      placeholder="Enter email address..."
                    />
                  </div>
                </div>
              </div>

              <!-- Social Media Section -->
              <div
                v-if="editedContent.sections.social"
                class="p-3 sm:p-4 lg:p-5 border-2 border-green-200 rounded-lg sm:rounded-xl hover:border-green-400 transition-all duration-300 bg-green-50/30"
              >
                <h3
                  class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2"
                >
                  <div
                    class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0"
                  >
                    <FontAwesomeIcon :icon="faGlobe" class="text-white text-xs sm:text-sm" />
                  </div>
                  <span>Social Media</span>
                </h3>

                <div class="space-y-3">
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      Social Section Title
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.social.title"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                      placeholder="Enter social section title..."
                    />
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                        Instagram
                      </label>
                      <input
                        type="text"
                        v-model="editedContent.social.instagram"
                        @blur="saveContent"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="@username"
                      />
                    </div>
                    <div>
                      <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                        Twitter
                      </label>
                      <input
                        type="text"
                        v-model="editedContent.social.twitter"
                        @blur="saveContent"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="@username"
                      />
                    </div>
                    <div>
                      <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                        Facebook
                      </label>
                      <input
                        type="text"
                        v-model="editedContent.social.facebook"
                        @blur="saveContent"
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                        placeholder="username"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- FAQ Section -->
              <div
                v-if="editedContent.sections.faq"
                class="p-3 sm:p-4 lg:p-5 border-2 border-orange-200 rounded-lg sm:rounded-xl hover:border-orange-400 transition-all duration-300 bg-orange-50/30"
              >
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                  <h3
                    class="text-base sm:text-lg font-bold text-gray-900 flex items-center space-x-2"
                  >
                    <div
                      class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0"
                    >
                      <FontAwesomeIcon
                        :icon="faQuestionCircle"
                        class="text-white text-xs sm:text-sm"
                      />
                    </div>
                    <span>FAQ Section</span>
                  </h3>
                  <button
                    @click="addFAQItem"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all flex items-center space-x-1"
                  >
                    <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                    <span>Add FAQ</span>
                  </button>
                </div>

                <div class="space-y-3">
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      FAQ Section Title
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.faq.title"
                      @blur="saveContent"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                      placeholder="Enter FAQ section title..."
                    />
                  </div>
                  <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                      FAQ Description
                    </label>
                    <textarea
                      v-model="editedContent.sections.faq.description"
                      @blur="saveContent"
                      rows="2"
                      class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm resize-vertical"
                      placeholder="Enter FAQ description..."
                    />
                  </div>

                  <div class="space-y-3">
                    <div
                      v-for="(faq, index) in editedContent.sections.faq.items"
                      :key="index"
                      class="p-3 sm:p-4 border-2 border-gray-200 rounded-lg bg-white relative"
                    >
                      <button
                        @click="removeFAQItem(index)"
                        class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-all"
                      >
                        <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                      </button>

                      <div class="space-y-2 pr-8">
                        <div>
                          <label class="block text-xs text-gray-600 mb-1 font-medium">
                            Question
                          </label>
                          <input
                            type="text"
                            v-model="faq.q"
                            @blur="saveContent"
                            class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-xs sm:text-sm"
                            placeholder="Enter question..."
                          />
                        </div>
                        <div>
                          <label class="block text-xs text-gray-600 mb-1 font-medium">Answer</label>
                          <textarea
                            v-model="faq.a"
                            @blur="saveContent"
                            rows="2"
                            class="w-full px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-xs sm:text-sm resize-vertical"
                            placeholder="Enter answer..."
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Gallery Editor Section -->
            <div v-if="page === 'gallery'" class="space-y-6">
              <!-- Gallery Hero Section -->
              <div
                v-if="editedContent.sections.hero"
                class="p-4 sm:p-6 border-2 border-purple-200 rounded-xl bg-purple-50/30"
              >
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center space-x-2">
                  <div
                    class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0"
                  >
                    <FontAwesomeIcon :icon="faImages" class="text-white text-sm" />
                  </div>
                  <span>Gallery Hero Section</span>
                </h3>

                <div class="space-y-4">
                  <!-- Badge -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Badge Text</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.badge"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="Welcome to Your Adventure Log"
                    />
                  </div>
                  <!-- Background Image -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Background Image URL
                    </label>
                    <input
                      type="url"
                      v-model="editedContent.sections.hero.backgroundImage"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="https://images.unsplash.com/photo-..."
                    />
                    <p class="text-xs text-gray-500 mt-1">
                      Use high-quality landscape images from Unsplash
                    </p>
                  </div>

                  <!-- Badge -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Badge Text</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.badge"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="Welcome to Your Adventure Log"
                    />
                  </div>

                  <!-- Main Title -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Main Title</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.title"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="Adventure Gallery"
                    />
                  </div>

                  <!-- Highlighted Title -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Highlighted Title (Gradient Text)
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.highlightedTitle"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="Visual Stories"
                    />
                  </div>

                  <!-- Subtitle -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.subtitle"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="Visual stories from incredible journeys around the world"
                    />
                  </div>

                  <!-- Stats Section -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stat 1 Label (Images)
                      </label>
                      <input
                        type="text"
                        v-model="editedContent.sections.hero.stat1Label"
                        @blur="saveContent"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                        placeholder="Photos Shared"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stat 2 Label (Adventures)
                      </label>
                      <input
                        type="text"
                        v-model="editedContent.sections.hero.stat2Label"
                        @blur="saveContent"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                        placeholder="Adventures Documented"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stat 3 Label (Countries)
                      </label>
                      <input
                        type="text"
                        v-model="editedContent.sections.hero.stat3Label"
                        @blur="saveContent"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                        placeholder="Countries Covered"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stat 4 Label (Community)
                      </label>
                      <input
                        type="text"
                        v-model="editedContent.sections.hero.stat4Label"
                        @blur="saveContent"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                        placeholder="Community Active"
                      />
                    </div>
                  </div>

                  <!-- Countries Covered Number -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Countries Covered Number
                    </label>
                    <input
                      type="number"
                      v-model="editedContent.sections.hero.countriesCovered"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="50"
                    />
                  </div>

                  <!-- Community Active Text -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Community Active Text
                    </label>
                    <input
                      type="text"
                      v-model="editedContent.sections.hero.communityActive"
                      @blur="saveContent"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                      placeholder="24/7"
                    />
                  </div>

                  <!-- Gallery Categories -->
                  <div>
                    <div class="flex items-center justify-between mb-2">
                      <label class="block text-sm font-medium text-gray-700">
                        Gallery Categories
                      </label>
                      <button
                        @click="addGalleryCategory"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all flex items-center space-x-1"
                      >
                        <FontAwesomeIcon :icon="faPlus" class="text-xs" />
                        <span>Add Category</span>
                      </button>
                    </div>

                    <div class="space-y-2">
                      <div
                        v-for="(category, index) in editedContent.sections.hero.categories || []"
                        :key="index"
                        class="p-3 border-2 border-gray-200 rounded-lg bg-white relative"
                      >
                        <button
                          @click="removeGalleryCategory(index)"
                          class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1 rounded-lg hover:bg-red-50 transition-all"
                        >
                          <FontAwesomeIcon :icon="faTrash" class="text-xs" />
                        </button>

                        <div class="pr-8">
                          <label class="block text-xs text-gray-600 mb-1 font-medium">
                            Category {{ index + 1 }}
                          </label>
                          <input
                            type="text"
                            v-model="editedContent.sections.hero.categories[index]"
                            @blur="saveContent"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-xs"
                            placeholder="e.g., Mountain Expeditions"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- CTA Cards -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- CTA 1 -->
                    <div class="p-3 border-2 border-amber-200 rounded-lg bg-amber-50/30">
                      <h4 class="text-xs font-semibold text-amber-800 mb-2">First CTA Card</h4>
                      <div class="space-y-2">
                        <div>
                          <label class="block text-xs text-amber-700 mb-1 font-medium">Title</label>
                          <input
                            type="text"
                            v-model="editedContent.sections.hero.cta1Title"
                            @blur="saveContent"
                            class="w-full px-2 py-1 border border-amber-300 rounded text-xs"
                            placeholder="Browse Gallery"
                          />
                        </div>
                        <div>
                          <label class="block text-xs text-amber-700 mb-1 font-medium">
                            Subtitle
                          </label>
                          <input
                            type="text"
                            v-model="editedContent.sections.hero.cta1Subtitle"
                            @blur="saveContent"
                            class="w-full px-2 py-1 border border-amber-300 rounded text-xs"
                            placeholder="Explore stunning visuals"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- CTA 2 -->
                    <div class="p-3 border-2 border-emerald-200 rounded-lg bg-emerald-50/30">
                      <h4 class="text-xs font-semibold text-emerald-800 mb-2">Second CTA Card</h4>
                      <div class="space-y-2">
                        <div>
                          <label class="block text-xs text-emerald-700 mb-1 font-medium">
                            Title
                          </label>
                          <input
                            type="text"
                            v-model="editedContent.sections.hero.cta2Title"
                            @blur="saveContent"
                            class="w-full px-2 py-1 border border-emerald-300 rounded text-xs"
                            placeholder="Share Your Story"
                          />
                        </div>
                        <div>
                          <label class="block text-xs text-emerald-700 mb-1 font-medium">
                            Subtitle
                          </label>
                          <input
                            type="text"
                            v-model="editedContent.sections.hero.cta2Subtitle"
                            @blur="saveContent"
                            class="w-full px-2 py-1 border border-emerald-300 rounded text-xs"
                            placeholder="Upload your adventures"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Gallery Images Management Section -->
              <div class="p-4 sm:p-6 border-2 border-purple-200 rounded-xl bg-purple-50/30">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center space-x-2">
                  <FontAwesomeIcon :icon="faImages" class="text-purple-600" />
                  <span>Gallery Images Management</span>
                </h3>

                <!-- Upload Section -->
                <div class="mb-6 p-4 bg-white rounded-lg border-2 border-dashed border-gray-300">
                  <h4 class="font-semibold text-gray-800 mb-3">Upload New Image</h4>
                  <div class="space-y-3">
                    <input
                      type="file"
                      ref="fileInput"
                      @change="handleFileUpload"
                      accept="image/*"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                    />
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <input
                        v-model="newImage.caption"
                        type="text"
                        placeholder="Image caption"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                      />
                      <input
                        v-model="newImage.location"
                        type="text"
                        placeholder="Location"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                      />
                    </div>
                    <button
                      @click="uploadImage"
                      :disabled="uploading"
                      class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 disabled:opacity-50 flex items-center space-x-2"
                    >
                      <FontAwesomeIcon
                        :icon="uploading ? faSpinner : faUpload"
                        :class="{
                          'animate-spin': uploading,
                        }"
                      />
                      <span>{{ uploading ? 'Uploading...' : 'Upload Image' }}</span>
                    </button>
                  </div>
                </div>

                <!-- Existing Images -->
                <div class="space-y-4">
                  <h4 class="font-semibold text-gray-800">
                    Existing Images ({{ editedContent.images?.length || 0 }})
                  </h4>

                  <!-- No Images State -->
                  <div
                    v-if="!editedContent.images?.length"
                    class="text-center py-12 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50/50"
                  >
                    <div
                      class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                      <FontAwesomeIcon :icon="faImages" class="text-purple-500 text-2xl" />
                    </div>
                    <h5 class="text-lg font-semibold text-gray-700 mb-2">No Images Uploaded Yet</h5>
                    <p class="text-gray-500 text-sm max-w-md mx-auto">
                      Upload your first adventure photo to start building your gallery. Images will
                      appear here once uploaded.
                    </p>
                  </div>

                  <!-- Images Grid -->
                  <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                      v-for="(image, index) in editedContent.images"
                      :key="image.id"
                      class="bg-white rounded-lg border border-gray-200 p-3"
                    >
                      <img
                        :src="image.url"
                        :alt="image.caption"
                        class="w-full h-32 object-cover rounded mb-2"
                      />
                      <input
                        v-model="image.caption"
                        @blur="updateImage(image)"
                        type="text"
                        class="w-full px-2 py-1 border border-gray-300 rounded text-sm mb-2"
                        placeholder="Caption"
                      />
                      <input
                        v-model="image.location"
                        @blur="updateImage(image)"
                        type="text"
                        class="w-full px-2 py-1 border border-gray-300 rounded text-sm mb-2"
                        placeholder="Location"
                      />
                      <button
                        @click="openDeleteDialog(image)"
                        class="w-full bg-red-500 text-white py-1 rounded text-sm hover:bg-red-600 flex items-center justify-center space-x-1 transition-all duration-200"
                      >
                        <FontAwesomeIcon :icon="faTrash" />
                        <span>Delete</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Simple Content Editor (for other pages) -->
            <div
              v-if="page !== 'home' && page !== 'about' && page !== 'contact'"
              class="p-3 sm:p-4 lg:p-5 border-2 border-gray-200 rounded-lg sm:rounded-xl hover:border-blue-300 transition-all duration-300 bg-white"
            >
              <div class="mb-3 sm:mb-4">
                <label
                  class="block text-sm font-semibold text-gray-700 mb-2 sm:mb-3 flex items-center space-x-2"
                >
                  <FontAwesomeIcon :icon="faTextWidth" class="text-blue-500 text-sm" />
                  <span class="text-xs sm:text-sm">Page Content</span>
                </label>
                <textarea
                  v-model="editedContent.content"
                  @blur="saveContent"
                  rows="6"
                  class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 resize-vertical text-sm"
                  placeholder="Enter your page content..."
                />
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="p-3 sm:p-4 lg:p-6 border-t border-gray-200 bg-gray-50/50 flex-shrink-0">
            <button
              @click="saveContent"
              :disabled="saving"
              class="group w-full bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-8 py-3 sm:py-4 rounded-lg sm:rounded-xl transition-all duration-300 hover:shadow-xl hover:scale-105 flex items-center justify-center space-x-2 sm:space-x-3 font-bold text-sm sm:text-lg disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <FontAwesomeIcon
                :icon="saving ? faSpinner : faCheckCircle"
                class="text-lg sm:text-xl group-hover:scale-110 transition-transform"
                :class="{ 'animate-spin': saving }"
              />
              <span>{{ saving ? 'Saving...' : 'Save All Changes' }}</span>
            </button>
          </div>
        </div>

        <!-- Live Preview Panel -->
        <div
          class="group bg-white shadow-lg sm:shadow-xl rounded-xl sm:rounded-2xl border border-gray-100 flex flex-col h-full overflow-hidden"
        >
          <div class="p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center space-x-2 sm:space-x-3">
              <div
                class="w-8 h-8 sm:w-12 sm:h-12 bg-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg flex-shrink-0"
              >
                <FontAwesomeIcon :icon="faDesktop" class="text-white text-sm sm:text-xl" />
              </div>
              <div class="min-w-0 flex-1">
                <h2 class="text-lg sm:text-2xl font-bold text-gray-900 truncate">Live Preview</h2>
                <p class="text-xs sm:text-sm text-gray-500">Real-time preview of your changes</p>
              </div>
            </div>
          </div>

          <!-- Scrollable Preview Content -->
          <div class="flex-1 overflow-y-auto p-3 sm:p-4 lg:p-6">
            <div
              class="border-2 border-dashed border-gray-300 rounded-xl p-4 sm:p-6 bg-gradient-to-br from-gray-50 to-blue-50/30 hover:border-blue-400 transition-all duration-300 h-full flex flex-col"
            >
              <div class="text-center mb-4 sm:mb-6">
                <FontAwesomeIcon
                  :icon="faEye"
                  class="text-blue-500 text-2xl sm:text-3xl mb-2 sm:mb-3"
                />
                <p class="text-gray-600 text-xs sm:text-sm">
                  Changes auto-save as you type. Preview updates in real-time.
                </p>
              </div>

              <!-- Mini Preview -->
              <div
                class="bg-white border-2 border-gray-200 rounded-xl p-3 sm:p-4 lg:p-5 shadow-lg hover:shadow-xl transition-all duration-300 mb-4 sm:mb-6 flex-1 overflow-y-auto"
              >
                <h3
                  class="text-lg sm:text-xl font-bold text-blue-600 mb-3 sm:mb-4 flex items-center space-x-2"
                >
                  <FontAwesomeIcon :icon="faFileAlt" class="text-blue-500 text-sm" />
                  <span class="truncate">{{ editedContent.title || 'Page Title' }}</span>
                </h3>

                <!-- Home Page Preview -->
                <div v-if="page === 'home'" class="space-y-3 sm:space-y-4">
                  <!-- Hero Preview -->
                  <!-- Enhanced Hero Preview -->
                  <div class="p-3 sm:p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                    <h4
                      class="font-bold text-purple-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-purple-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faPalette" class="text-white text-xs" />
                      </div>
                      <span>Hero Section</span>
                    </h4>
                    <p class="text-xs text-purple-600 font-semibold mb-1">
                      {{ editedContent.sections.hero?.badge }}
                    </p>
                    <p class="text-xs text-gray-700 font-semibold mb-1">
                      {{ editedContent.sections.hero?.title }}
                    </p>
                    <p class="text-xs text-gray-600 mb-2">
                      {{ editedContent.sections.hero?.subtitle?.substring(0, 80) }}...
                    </p>
                    <div class="grid grid-cols-2 gap-1">
                      <div
                        v-for="(stat, i) in editedContent.sections.hero?.stats?.slice(0, 2)"
                        :key="i"
                        class="text-xs bg-white rounded p-1"
                      >
                        <span class="font-bold text-purple-600">{{ stat.number }}</span>
                        <span class="text-gray-600 text-xs">
                          {{ stat.label }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Features Preview -->
                  <div class="p-3 sm:p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <h4
                      class="font-bold text-blue-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-blue-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faList" class="text-white text-xs" />
                      </div>
                      <span>
                        Features ({{ editedContent.sections.features?.items?.length || 0 }})
                      </span>
                    </h4>
                    <p class="text-xs text-gray-700 font-semibold mb-2">
                      {{ editedContent.sections.features?.title }}
                    </p>
                    <div class="space-y-1">
                      <div
                        v-for="(item, i) in editedContent.sections.features?.items?.slice(0, 2)"
                        :key="i"
                        class="text-xs text-gray-600"
                      >
                        • {{ item.title }}
                      </div>
                    </div>
                  </div>

                  <!-- Mission Preview -->
                  <div class="p-3 sm:p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                    <h4
                      class="font-bold text-green-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-green-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faChartBar" class="text-white text-xs" />
                      </div>
                      <span>
                        Mission & Stats ({{ editedContent.sections.mission?.stats?.length || 0 }})
                      </span>
                    </h4>
                    <p class="text-xs text-gray-700 font-semibold mb-1">
                      {{ editedContent.sections.mission?.title }}
                    </p>
                    <p class="text-xs text-gray-600 mb-2">
                      {{ editedContent.sections.mission?.content?.substring(0, 60) }}...
                    </p>
                    <div class="grid grid-cols-2 gap-1">
                      <div
                        v-for="(stat, i) in editedContent.sections.mission?.stats?.slice(0, 4)"
                        :key="i"
                        class="text-xs bg-white rounded p-1.5"
                      >
                        <span class="font-bold text-green-600">{{ stat.number }}</span>
                        <span class="text-gray-600 text-xs">
                          {{ stat.label }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Recent Adventures Preview -->
                  <div class="p-3 sm:p-4 bg-orange-50 border-2 border-orange-200 rounded-lg">
                    <h4
                      class="font-bold text-orange-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-orange-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faImage" class="text-white text-xs" />
                      </div>
                      <span>{{ editedContent.sections.recent?.title }}</span>
                    </h4>
                    <p class="text-xs text-gray-600">Dynamic - shows your actual adventures</p>
                  </div>
                </div>

                <!-- About Page Preview -->
                <div v-else-if="page === 'about'" class="space-y-3 sm:space-y-4">
                  <!-- Hero Preview -->
                  <div class="p-3 sm:p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                    <h4
                      class="font-bold text-purple-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-purple-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faPalette" class="text-white text-xs" />
                      </div>
                      <span>Hero Section</span>
                    </h4>
                    <p class="text-xs text-gray-700 font-semibold mb-1">
                      {{ editedContent.sections.hero?.title }}
                    </p>
                    <p class="text-xs text-gray-600">
                      {{ editedContent.sections.hero?.subtitle?.substring(0, 80) }}...
                    </p>
                  </div>

                  <!-- Mission Preview -->
                  <div class="p-3 sm:p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                    <h4
                      class="font-bold text-green-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-green-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faLightbulb" class="text-white text-xs" />
                      </div>
                      <span>Mission Section</span>
                    </h4>
                    <p class="text-xs text-gray-700 font-semibold mb-1">
                      {{ editedContent.sections.mission?.title }}
                    </p>
                    <p class="text-xs text-gray-600">
                      {{ editedContent.sections.mission?.heading?.substring(0, 60) }}...
                    </p>
                    <div class="space-y-1 mt-2">
                      <div
                        v-for="(point, i) in editedContent.sections.mission?.points?.slice(0, 2)"
                        :key="i"
                        class="text-xs text-gray-600"
                      >
                        •
                        {{ point.substring(0, 50) }}...
                      </div>
                    </div>
                  </div>

                  <!-- Feature Cards Preview -->
                  <div class="p-3 sm:p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                    <h4
                      class="font-bold text-purple-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-purple-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faList" class="text-white text-xs" />
                      </div>
                      <span>
                        Feature Cards ({{ editedContent.sections.featureCards?.length || 0 }})
                      </span>
                    </h4>
                    <div class="space-y-1">
                      <div
                        v-for="(card, i) in editedContent.sections.featureCards?.slice(0, 2)"
                        :key="i"
                        class="text-xs text-gray-600"
                      >
                        • {{ card.title }}
                      </div>
                    </div>
                  </div>

                  <!-- Stats Preview -->
                  <div class="p-3 sm:p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <h4
                      class="font-bold text-blue-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-blue-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faChartBar" class="text-white text-xs" />
                      </div>
                      <span>Stats Section</span>
                    </h4>
                    <div class="grid grid-cols-2 gap-1">
                      <div
                        v-for="(value, key) in editedContent.stats"
                        :key="key"
                        class="text-xs bg-white rounded p-1.5"
                      >
                        <span class="font-bold text-blue-600">{{ value }}</span>
                        <span class="text-gray-600 text-xs">
                          {{ key.replace('_', ' ') }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Contact Page Preview -->
                <div v-else-if="page === 'contact'" class="space-y-3 sm:space-y-4">
                  <!-- Hero Preview -->
                  <div class="p-3 sm:p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                    <h4
                      class="font-bold text-purple-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-purple-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faPalette" class="text-white text-xs" />
                      </div>
                      <span>Hero Section</span>
                    </h4>
                    <p class="text-xs text-gray-700 font-semibold mb-1">
                      {{ editedContent.sections.hero?.title }}
                    </p>
                    <p class="text-xs text-gray-600">
                      {{ editedContent.sections.hero?.subtitle?.substring(0, 80) }}...
                    </p>
                  </div>

                  <!-- Contact Info Preview -->
                  <div class="p-3 sm:p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <h4
                      class="font-bold text-blue-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-blue-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faMapMarkedAlt" class="text-white text-xs" />
                      </div>
                      <span>Contact Information</span>
                    </h4>
                    <p class="text-xs text-gray-700 font-semibold mb-1">
                      {{ editedContent.sections.info?.title }}
                    </p>
                    <p class="text-xs text-gray-600">
                      {{ editedContent.sections.info?.description?.substring(0, 60) }}...
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                      {{ editedContent.email }}
                    </p>
                  </div>

                  <!-- Social Media Preview -->
                  <div class="p-3 sm:p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                    <h4
                      class="font-bold text-green-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-green-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faGlobe" class="text-white text-xs" />
                      </div>
                      <span>Social Media</span>
                    </h4>
                    <p class="text-xs text-gray-700 font-semibold mb-1">
                      {{ editedContent.sections.social?.title }}
                    </p>
                    <div class="space-y-1">
                      <div v-if="editedContent.social?.instagram" class="text-xs text-gray-600">
                        • Instagram:
                        {{ editedContent.social.instagram }}
                      </div>
                      <div v-if="editedContent.social?.twitter" class="text-xs text-gray-600">
                        • Twitter:
                        {{ editedContent.social.twitter }}
                      </div>
                      <div v-if="editedContent.social?.facebook" class="text-xs text-gray-600">
                        • Facebook:
                        {{ editedContent.social.facebook }}
                      </div>
                    </div>
                  </div>

                  <!-- FAQ Preview -->
                  <div class="p-3 sm:p-4 bg-orange-50 border-2 border-orange-200 rounded-lg">
                    <h4
                      class="font-bold text-orange-900 text-xs sm:text-sm mb-2 flex items-center space-x-2"
                    >
                      <div class="w-4 h-4 bg-orange-600 rounded flex items-center justify-center">
                        <FontAwesomeIcon :icon="faQuestionCircle" class="text-white text-xs" />
                      </div>
                      <span>FAQ Section</span>
                    </h4>
                    <p class="text-xs text-gray-700 font-semibold mb-1">
                      {{ editedContent.sections.faq?.title }}
                    </p>
                    <p class="text-xs text-gray-600 mb-2">
                      {{ editedContent.sections.faq?.description }}
                    </p>
                    <div class="space-y-1">
                      <div
                        v-for="(faq, i) in editedContent.sections.faq?.items?.slice(0, 2)"
                        :key="i"
                        class="text-xs text-gray-600"
                      >
                        • {{ faq.q }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Other Pages Preview -->
                <div v-else class="p-2 sm:p-3 bg-gray-50 rounded-lg">
                  <p class="text-gray-600 text-xs sm:text-sm">
                    {{ editedContent.content?.substring(0, 150) || 'No content yet...' }}
                  </p>
                </div>
              </div>

              <!-- Quick Preview Link -->
              <div class="text-center mt-auto">
                <Link
                  :href="route('website.page', { page })"
                  target="_blank"
                  class="group inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-105 font-semibold text-sm sm:text-base w-full sm:w-auto justify-center"
                >
                  <FontAwesomeIcon
                    :icon="faExternalLinkAlt"
                    class="group-hover:scale-110 transition-transform text-sm"
                  />
                  <span>Open Full Preview</span>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Add the delete confirmation dialog at the end of your template -->
  <DeleteImageConfirmationDialog
    :show="showDeleteDialog"
    :image="imageToDelete"
    :deleting="deleting"
    @confirm="deleteImage"
    @cancel="cancelDelete"
  />
</template>

<style scoped>
  /* Custom scrollbar styling */
  .overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
  }

  .overflow-y-auto::-webkit-scrollbar {
    width: 4px;
  }

  .overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }

  @media (min-width: 640px) {
    .overflow-y-auto::-webkit-scrollbar {
      width: 6px;
    }
  }
</style>
