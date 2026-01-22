import { useEffect, useState } from 'react'
import Hero from '../components/Hero'
import Categories from '../components/Categories'
import BlogCategoryCard from '../components/BlogCategoryCard'
import FeaturedBlogCard from '../components/FeaturedBlogCard'
import BlogCard from '../components/BlogCard'
import BlogSidebar from '../components/BlogSidebar'
import CityWiseSection from '../components/CityWiseSection'
import BlogCTA from '../components/BlogCTA'
import { blogAPI, categoryAPI } from '../services/api'
import './Blog.css'

function Blog() {
  const [featuredBlogs, setFeaturedBlogs] = useState([])
  const [latestBlogs, setLatestBlogs] = useState([])
  const [categories, setCategories] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  useEffect(() => {
    document.title = 'IndiaPropertys Blog – Property News, Guides & Tips | Real Estate Insights'
    const metaDescription = document.querySelector('meta[name="description"]')
    if (metaDescription) {
      metaDescription.setAttribute('content', 'Latest real estate trends, buying guides, legal tips & investment insights across India. Expert property advice for buyers, sellers & investors.')
    } else {
      const meta = document.createElement('meta')
      meta.name = 'description'
      meta.content = 'Latest real estate trends, buying guides, legal tips & investment insights across India. Expert property advice for buyers, sellers & investors.'
      document.getElementsByTagName('head')[0].appendChild(meta)
    }

    // Fetch data from API
    fetchBlogs()
    fetchCategories()
  }, [])

  const fetchBlogs = async () => {
    try {
      setLoading(true)
      
      // Fetch featured blogs
      const featuredResponse = await blogAPI.getAll({ featured: true, per_page: 4 })
      if (featuredResponse.success) {
        setFeaturedBlogs(featuredResponse.data.map(blog => ({
          image: blog.image_url || 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6',
          category: blog.category_name || 'General',
          title: blog.title,
          excerpt: blog.excerpt || '',
          link: `/post/${blog.slug}`
        })))
      }

      // Fetch latest blogs
      const latestResponse = await blogAPI.getAll({ per_page: 9 })
      if (latestResponse.success) {
        setLatestBlogs(latestResponse.data.map(blog => ({
          image: blog.image_url || 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6',
          tag: blog.category_name || 'General',
          title: blog.title,
          description: blog.excerpt || '',
          link: `/post/${blog.slug}`
        })))
      }
    } catch (err) {
      console.error('Error fetching blogs:', err)
      setError(err.message || 'Failed to load blogs')
    } finally {
      setLoading(false)
    }
  }

  // Helper function to map category name/slug to route path
  const getCategoryRoute = (categoryName, categorySlug) => {
    const name = (categoryName || '').toLowerCase().trim()
    const slug = (categorySlug || '').toLowerCase().trim()
    
    // Map category names/slugs to routes
    const categoryMap = {
      'buy': '/buy',
      'rent': '/rent',
      'investment': '/investment',
      'legal': '/legal',
      'tips': '/tips',
      'news': '/news'
    }
    
    // Check by name first, then by slug
    return categoryMap[name] || categoryMap[slug] || `/blog?category=${slug}`
  }

  const fetchCategories = async () => {
    try {
      const response = await categoryAPI.getAll()
      if (response.success) {
        // Map API categories to component format
        const mappedCategories = response.data.map(cat => ({
          icon: cat.icon || '📋',
          title: cat.name,
          description: cat.description || '',
          link: getCategoryRoute(cat.name, cat.slug)
        }))
        setCategories(mappedCategories)
      }
    } catch (err) {
      console.error('Error fetching categories:', err)
    }
  }

  if (loading) {
    return (
      <div style={{ padding: '2rem', textAlign: 'center' }}>
        <p>Loading blogs...</p>
      </div>
    )
  }

  if (error) {
    return (
      <div style={{ padding: '2rem', textAlign: 'center' }}>
        <p>Error: {error}</p>
        <button onClick={fetchBlogs}>Retry</button>
      </div>
    )
  }

  return (
    <>
      <Hero 
        title="IndiaPropertys Blog – Property News, Guides & Tips"
        subtitle="Latest real estate trends, buying guides, legal tips & investment insights across India"
      />
      <Categories />
      
      {/* Blog Categories Section */}
      <section className="blog-categories-section">
        <div className="blog-categories-container">
          <div className="blog-categories-grid">
            {categories.length > 0 ? (
              categories.map((category, index) => (
                <BlogCategoryCard
                  key={index}
                  icon={category.icon}
                  title={category.title}
                  description={category.description}
                  link={category.link}
                />
              ))
            ) : (
              <p>No categories available</p>
            )}
          </div>
        </div>
      </section>

      {/* Featured Blogs Section */}
      <section className="featured-blogs-section">
        <div className="featured-blogs-container">
          <h2 className="section-title">Featured Articles</h2>
          <div className="featured-blogs-grid">
            {featuredBlogs.length > 0 ? (
              featuredBlogs.map((blog, index) => (
                <FeaturedBlogCard
                  key={index}
                  image={blog.image}
                  category={blog.category}
                  title={blog.title}
                  excerpt={blog.excerpt}
                  link={blog.link}
                />
              ))
            ) : (
              <p>No featured blogs available</p>
            )}
          </div>
        </div>
      </section>

      {/* Main Content with Sidebar */}
      <section className="blog-main-content">
        <div className="blog-main-container">
          <div className="blog-content-wrapper">
            {/* Latest Blogs */}
            <div className="latest-blogs-section">
              <h2 className="section-title">Latest Articles</h2>
              <section className="blog-grid">
                {latestBlogs.length > 0 ? (
                  latestBlogs.map((blog, index) => (
                    <BlogCard
                      key={index}
                      image={blog.image}
                      tag={blog.tag}
                      title={blog.title}
                      description={blog.description}
                      link={blog.link}
                    />
                  ))
                ) : (
                  <p>No blogs available</p>
                )}
              </section>
            </div>

            {/* Sidebar */}
            <BlogSidebar />
          </div>
        </div>
      </section>

      {/* City-wise Section */}
      <CityWiseSection />

      {/* CTA Section */}
      <BlogCTA />
    </>
  )
}

export default Blog

